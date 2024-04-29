--
-- PostgreSQL database dump
--

-- Dumped from database version 15.5
-- Dumped by pg_dump version 15.5

-- Started on 2023-12-21 11:47:49

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 241 (class 1255 OID 16399)
-- Name: calendario(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.calendario() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE

cor varchar;
an int;
BEGIN

select idcorso into cor from insegnamento where idinsegnamento= new.idinsegnamento;
select anno into an  from insegnamento where idinsegnamento= new.idinsegnamento;


	if exists(select a.dataappello from appelli as a 
			  inner join insegnamento ON insegnamento.idinsegnamento = a.idinsegnamento 
			  where anno=an and new.dataappello= a.dataappello and idcorso=cor)
	then
			            RAISE EXCEPTION 'data gia occupata' ;
end if;


RETURN NEW;
END;
$$;


ALTER FUNCTION public.calendario() OWNER TO postgres;

--
-- TOC entry 242 (class 1255 OID 16554)
-- Name: check_prof(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.check_prof() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   if exists (select * from corso_di_laurea as c inner join insegnamento as i ON i.idcorso = c.idcorso inner join persona as p on p.idpersona = i.idpersona
	where p.idpersona=new.idpersona and c.idcorso= new.idcorso)
	then RAISE EXCEPTION 'IL PROFESSORE INSEGNA IN QUESTO CORSO';
	END IF;
RETURN NEW;
END;
$$;


ALTER FUNCTION public.check_prof() OWNER TO postgres;

--
-- TOC entry 225 (class 1255 OID 16400)
-- Name: creazione_insegnamento(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.creazione_insegnamento() RETURNS trigger
    LANGUAGE plpgsql
    AS $$  -- Conta quanti insegnamenti attivi ha il docente
    DECLARE
        insegnamenti_attivi integer;
    BEGIN

        -- Conta gli insegnamenti attivi del docente
        SELECT COUNT(*) INTO insegnamenti_attivi
        FROM public.insegnamento
        WHERE codicedocente = new.codicedocente;

        -- Se il docente ha già 3 insegnamenti attivi, l'inserimento viene impedito
        IF  insegnamenti_attivi=3 THEN
            RAISE EXCEPTION 'Il docente ha già 3 insegnamenti attivi %',new.codicedocente ;
        END IF;

        RETURN NEW;
    END;$$;


ALTER FUNCTION public.creazione_insegnamento() OWNER TO postgres;

--
-- TOC entry 240 (class 1255 OID 16401)
-- Name: insert_prova(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.insert_prova() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE

cor varchar;
post int;
ins varchar;
BEGIN

select s.idcorso inTO cor from  studente as s inner join corso_di_laurea ON corso_di_laurea.idcorso = s.idcorso where s.matricola=new.matricola;
select idinsegnamento inTO ins from appelli where idappello= new.idappello;
select posti into post from appelli where idappello= new.idappello;

if not exists(select * from insegnamento where idcorso=cor and idinsegnamento=ins)
	then 
			
	  RAISE EXCEPTION 'Lustente non piò escriversi a questo appello' ;		
			
end if;
	
if post=0
	then 
 RAISE EXCEPTION 'Posti finiti' ;	
			
end if;
if exists(select * from propedeutico where idinsegnamento_dipendente=ins)
	then
if not exists(select * from provare as p 
	inner join appelli as a on p.idappello = a.idappello 
	inner join propedeutico as pr on pr.idinsegnamento = a.idinsegnamento
	where pr.idinsegnamento_dipendente = ins and p.ritirato='false' and p.voto>=18 and p.matricola=new.matricola)
then
 RAISE EXCEPTION 'PERIODICITA NON RISPETTATE' ;	
end if;
end if;

UPDATE public.appelli
	SET posti=posti-1
	WHERE idappello=new.idappello ;
	
	
	
	RETURN NEW;
END;
$$;


ALTER FUNCTION public.insert_prova() OWNER TO postgres;

--
-- TOC entry 226 (class 1255 OID 16552)
-- Name: laurea(character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.laurea(mat character varying, voto integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
begin
 delete from studente where matricola=mat ;
 UPDATE studente_storico
	SET votolaurea=voto, ritirato=false
	WHERE matricola=mat;  
end;
$$;


ALTER FUNCTION public.laurea(mat character varying, voto integer) OWNER TO postgres;

--
-- TOC entry 238 (class 1255 OID 16402)
-- Name: propedeutico(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.propedeutico() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
cor1 varchar;
cor2 varchar;

anno1 varchar;
anno2 varchar;

BEGIN

select anno into anno1 from insegnamento where  idinsegnamento=new.idinsegnamento_dipendente;
select anno into anno2 from insegnamento where  idinsegnamento=new.idinsegnamento;

select idcorso into cor1 from insegnamento where  idinsegnamento=new.idinsegnamento_dipendente;
select idcorso into cor2 from insegnamento where  idinsegnamento=new.idinsegnamento;

if anno2>=anno1 
then 
RAISE EXCEPTION 'impossibile inserire propedeuticià' ;
end if;
if cor1!=cor2
then 
RAISE EXCEPTION 'impossibile inserire propedeuticià' ;
end if;




	RETURN NEW;
END;
$$;


ALTER FUNCTION public.propedeutico() OWNER TO postgres;

--
-- TOC entry 239 (class 1255 OID 16403)
-- Name: sposta_studente(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.sposta_studente() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare 
begin

INSERT INTO public.studente_storico(
	idcorso,idpersona, matricola, dataiscrizione, password)
	values(old.idcorso,old.idpersona, old.matricola, old.dataiscrizione,old.password);
INSERT INTO public.provare_storico(
	idappello, matricola, idpersona, voto, datainscrizione, ritirato)
	select idappello, matricola,idpersona, voto, datainscrizione, ritirato from provare where matricola=old.matricola;

	delete from provare where matricola=old.matricola;
	
	
	return old;
END;

$$;


ALTER FUNCTION public.sposta_studente() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 214 (class 1259 OID 16404)
-- Name: appelli; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.appelli (
    idappello character varying(10) NOT NULL,
    dataappello date NOT NULL,
    idinsegnamento character varying(10) NOT NULL,
    posti integer NOT NULL,
    CONSTRAINT appelli_posti_check CHECK ((posti > 0))
);


ALTER TABLE public.appelli OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 16408)
-- Name: corso_di_laurea; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.corso_di_laurea (
    idcorso character varying(10) NOT NULL,
    durata integer NOT NULL,
    descrizione character varying NOT NULL,
    nome character varying NOT NULL
);


ALTER TABLE public.corso_di_laurea OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 16413)
-- Name: docente; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.docente (
    codicedocente character varying(10) NOT NULL,
    password character varying(10) NOT NULL,
    dataiscrizione date DEFAULT CURRENT_DATE NOT NULL,
    idpersona character varying(10) NOT NULL
);


ALTER TABLE public.docente OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 16417)
-- Name: insegnamento; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.insegnamento (
    idpersona character varying(10) NOT NULL,
    codicedocente character varying(10) NOT NULL,
    idcorso character varying(10) NOT NULL,
    idinsegnamento character varying(10) NOT NULL,
    nome character varying NOT NULL,
    crediti integer NOT NULL,
    descrizione character varying NOT NULL,
    durata integer NOT NULL,
    anno integer NOT NULL,
    CONSTRAINT insegnamento_anno_check CHECK (((anno > 0) AND (anno <= 3))),
    CONSTRAINT insegnamento_durata_check CHECK ((durata > 0))
);


ALTER TABLE public.insegnamento OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 16424)
-- Name: persona; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.persona (
    idpersona character varying(10) NOT NULL,
    nome character varying NOT NULL,
    cognome character varying NOT NULL,
    cittadinascita character varying NOT NULL,
    datadinascita date NOT NULL
);


ALTER TABLE public.persona OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 16429)
-- Name: propedeutico; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.propedeutico (
    idinsegnamento character varying(10) NOT NULL,
    idinsegnamento_dipendente character varying(10) NOT NULL,
    CONSTRAINT propedeutico_check CHECK (((idinsegnamento)::text <> (idinsegnamento_dipendente)::text))
);


ALTER TABLE public.propedeutico OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 16433)
-- Name: provare; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.provare (
    idappello character varying(10) NOT NULL,
    idpersona character varying(10) NOT NULL,
    matricola character varying(10) NOT NULL,
    voto integer,
    datainscrizione date DEFAULT CURRENT_DATE NOT NULL,
    ritirato boolean DEFAULT false NOT NULL
);


ALTER TABLE public.provare OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 16438)
-- Name: provare_storico; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.provare_storico (
    idappello character varying(10) NOT NULL,
    matricola character varying(10) NOT NULL,
    idpersona character varying(10) NOT NULL,
    voto integer,
    datainscrizione date DEFAULT CURRENT_DATE NOT NULL,
    ritirato boolean NOT NULL
);


ALTER TABLE public.provare_storico OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 16442)
-- Name: segreteria; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.segreteria (
    codicesegreteria character varying(10) NOT NULL,
    password character varying(10) NOT NULL,
    dataiscrizione date DEFAULT CURRENT_DATE NOT NULL,
    idpersona character varying(10) NOT NULL
);


ALTER TABLE public.segreteria OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 16446)
-- Name: studente; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.studente (
    matricola character varying(10) NOT NULL,
    password character varying(10) NOT NULL,
    dataiscrizione date DEFAULT CURRENT_DATE NOT NULL,
    idpersona character varying(10) NOT NULL,
    idcorso character varying(10) NOT NULL
);


ALTER TABLE public.studente OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 16450)
-- Name: studente_storico; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.studente_storico (
    idcorso character varying(10) NOT NULL,
    idpersona character varying(10) NOT NULL,
    matricola character varying(10) NOT NULL,
    dataiscrizione date DEFAULT CURRENT_DATE NOT NULL,
    password character varying(10) NOT NULL,
    votolaurea integer,
    ritirato boolean DEFAULT true NOT NULL
);


ALTER TABLE public.studente_storico OWNER TO postgres;

--
-- TOC entry 3420 (class 0 OID 16404)
-- Dependencies: 214
-- Data for Name: appelli; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.appelli (idappello, dataappello, idinsegnamento, posti) FROM stdin;
lDO5jIsbl0	2022-02-20	uhf4aZgMXH	49
d4SDzs6DlI	2022-01-20	uhf4aZgMXH	48
LscV1p3rmV	2022-02-20	A773ye3zS7	48
bb3bAVyBvI	2023-07-20	0Ho3lJVxAT	15
iQQmgec4e6	2023-12-20	2icOpoAZJ6	49
xPs9icjQrQ	2023-11-20	2icOpoAZJ6	48
Z57rSKVL2e	2023-02-20	p4QfYLgWaE	8
xq3UiLeYQ1	2023-12-31	uhf4aZgMXH	49
\.


--
-- TOC entry 3421 (class 0 OID 16408)
-- Dependencies: 215
-- Data for Name: corso_di_laurea; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.corso_di_laurea (idcorso, durata, descrizione, nome) FROM stdin;
LpldkyuG2B	5	Corso di Matematica	Matematica
0VRlGGcUfz	5	corso di informatica	Informatica
\.


--
-- TOC entry 3422 (class 0 OID 16413)
-- Dependencies: 216
-- Data for Name: docente; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.docente (codicedocente, password, dataiscrizione, idpersona) FROM stdin;
LL6RQ2ACFH	FEYzyhfl0O	2023-12-17	yfB75a1t5Q
duM0xvqrbL	CiUo5quHY0	2023-12-20	RlMzd21nrh
\.


--
-- TOC entry 3423 (class 0 OID 16417)
-- Dependencies: 217
-- Data for Name: insegnamento; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.insegnamento (idpersona, codicedocente, idcorso, idinsegnamento, nome, crediti, descrizione, durata, anno) FROM stdin;
RlMzd21nrh	duM0xvqrbL	LpldkyuG2B	p4QfYLgWaE	Calcolo 2	12	Corso di calcolo	5	3
RlMzd21nrh	duM0xvqrbL	LpldkyuG2B	0Ho3lJVxAT	Informatica	6	Corso di informatica	3	3
yfB75a1t5Q	LL6RQ2ACFH	LpldkyuG2B	uhf4aZgMXH	Analisi	12	Corso di analisi	6	1
yfB75a1t5Q	LL6RQ2ACFH	LpldkyuG2B	2icOpoAZJ6	Analisi 2	12	Corso di analisi	6	2
yfB75a1t5Q	LL6RQ2ACFH	LpldkyuG2B	A773ye3zS7	Calcolo 1	6	Corso di calcolo	6	2
RlMzd21nrh	duM0xvqrbL	LpldkyuG2B	laev91G4ny	Sistemi Operativi	12	Corso di s.o.	5	2
\.


--
-- TOC entry 3424 (class 0 OID 16424)
-- Dependencies: 218
-- Data for Name: persona; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.persona (idpersona, nome, cognome, cittadinascita, datadinascita) FROM stdin;
6789012345	Giulia	Martini	Venezia	1994-02-18
RlMzd21nrh	Lucia	Carone	MIlano	2002-06-19
0Y687JAnyX	Elisa	Scaggioni	Verona	2003-05-05
yfB75a1t5Q	Gianfranco	Pero	Roma	1974-07-17
KPvDz52cl1	Simone	Casciaro	MIlano	2001-01-20
TsKiAj8saY	Luca	Airolde	Milano	1999-05-20
\.


--
-- TOC entry 3425 (class 0 OID 16429)
-- Dependencies: 219
-- Data for Name: propedeutico; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.propedeutico (idinsegnamento, idinsegnamento_dipendente) FROM stdin;
uhf4aZgMXH	2icOpoAZJ6
A773ye3zS7	p4QfYLgWaE
uhf4aZgMXH	laev91G4ny
\.


--
-- TOC entry 3426 (class 0 OID 16433)
-- Dependencies: 220
-- Data for Name: provare; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.provare (idappello, idpersona, matricola, voto, datainscrizione, ritirato) FROM stdin;
lDO5jIsbl0	0Y687JAnyX	uInRLFZwx6	15	2022-02-20	t
d4SDzs6DlI	0Y687JAnyX	uInRLFZwx6	24	2022-01-20	f
bb3bAVyBvI	0Y687JAnyX	uInRLFZwx6	30	2023-07-20	f
LscV1p3rmV	0Y687JAnyX	uInRLFZwx6	19	2022-02-20	f
iQQmgec4e6	0Y687JAnyX	uInRLFZwx6	\N	2023-12-20	t
xPs9icjQrQ	0Y687JAnyX	uInRLFZwx6	\N	2023-11-20	t
xq3UiLeYQ1	0Y687JAnyX	uInRLFZwx6	\N	2023-12-20	f
\.


--
-- TOC entry 3427 (class 0 OID 16438)
-- Dependencies: 221
-- Data for Name: provare_storico; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.provare_storico (idappello, matricola, idpersona, voto, datainscrizione, ritirato) FROM stdin;
d4SDzs6DlI	H8zsRojcbO	KPvDz52cl1	20	2022-01-20	f
LscV1p3rmV	H8zsRojcbO	KPvDz52cl1	24	2022-02-20	f
bb3bAVyBvI	H8zsRojcbO	KPvDz52cl1	26	2023-07-20	f
xPs9icjQrQ	H8zsRojcbO	KPvDz52cl1	30	2023-11-20	f
Z57rSKVL2e	H8zsRojcbO	KPvDz52cl1	19	2023-02-20	f
\.


--
-- TOC entry 3428 (class 0 OID 16442)
-- Dependencies: 222
-- Data for Name: segreteria; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.segreteria (codicesegreteria, password, dataiscrizione, idpersona) FROM stdin;
SEGRET0010	password6	2023-06-10	6789012345
\.


--
-- TOC entry 3429 (class 0 OID 16446)
-- Dependencies: 223
-- Data for Name: studente; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.studente (matricola, password, dataiscrizione, idpersona, idcorso) FROM stdin;
uInRLFZwx6	bpyO9Ts4ZP	2023-12-19	0Y687JAnyX	LpldkyuG2B
\.


--
-- TOC entry 3430 (class 0 OID 16450)
-- Dependencies: 224
-- Data for Name: studente_storico; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.studente_storico (idcorso, idpersona, matricola, dataiscrizione, password, votolaurea, ritirato) FROM stdin;
LpldkyuG2B	KPvDz52cl1	H8zsRojcbO	2023-12-20	1HsEqXEG83	100	f
\.


--
-- TOC entry 3236 (class 2606 OID 16456)
-- Name: appelli appelli_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appelli
    ADD CONSTRAINT appelli_pkey PRIMARY KEY (idappello);


--
-- TOC entry 3229 (class 2606 OID 16457)
-- Name: corso_di_laurea corso_di_laurea_durata_check; Type: CHECK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE public.corso_di_laurea
    ADD CONSTRAINT corso_di_laurea_durata_check CHECK (((durata = 3) OR (durata = 5))) NOT VALID;


--
-- TOC entry 3238 (class 2606 OID 16459)
-- Name: corso_di_laurea corso_di_laurea_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.corso_di_laurea
    ADD CONSTRAINT corso_di_laurea_pkey PRIMARY KEY (idcorso);


--
-- TOC entry 3228 (class 2606 OID 16574)
-- Name: appelli data_check; Type: CHECK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE public.appelli
    ADD CONSTRAINT data_check CHECK ((dataappello >= CURRENT_DATE)) NOT VALID;


--
-- TOC entry 3240 (class 2606 OID 16462)
-- Name: docente docente_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT docente_pkey PRIMARY KEY (codicedocente, idpersona);


--
-- TOC entry 3242 (class 2606 OID 16464)
-- Name: insegnamento insegnamento_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.insegnamento
    ADD CONSTRAINT insegnamento_pkey PRIMARY KEY (idinsegnamento);


--
-- TOC entry 3244 (class 2606 OID 16466)
-- Name: persona persona_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persona
    ADD CONSTRAINT persona_pkey PRIMARY KEY (idpersona);


--
-- TOC entry 3246 (class 2606 OID 16468)
-- Name: propedeutico propedeutico_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.propedeutico
    ADD CONSTRAINT propedeutico_pkey PRIMARY KEY (idinsegnamento, idinsegnamento_dipendente);


--
-- TOC entry 3248 (class 2606 OID 16470)
-- Name: provare provare_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.provare
    ADD CONSTRAINT provare_pkey PRIMARY KEY (idappello, idpersona, matricola);


--
-- TOC entry 3250 (class 2606 OID 16560)
-- Name: provare_storico provare_storico_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.provare_storico
    ADD CONSTRAINT provare_storico_pkey PRIMARY KEY (idappello, matricola, idpersona);


--
-- TOC entry 3233 (class 2606 OID 16571)
-- Name: provare provare_voto_check; Type: CHECK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE public.provare
    ADD CONSTRAINT provare_voto_check CHECK (((voto >= 0) AND (voto <= 31))) NOT VALID;


--
-- TOC entry 3252 (class 2606 OID 16475)
-- Name: segreteria segreteria_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT segreteria_pkey PRIMARY KEY (codicesegreteria, idpersona);


--
-- TOC entry 3254 (class 2606 OID 16477)
-- Name: studente studente_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT studente_pkey PRIMARY KEY (matricola, idpersona);


--
-- TOC entry 3256 (class 2606 OID 16479)
-- Name: studente_storico studente_storico_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.studente_storico
    ADD CONSTRAINT studente_storico_pkey PRIMARY KEY (matricola, idpersona);


--
-- TOC entry 3234 (class 2606 OID 16572)
-- Name: studente_storico votolaurea; Type: CHECK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE public.studente_storico
    ADD CONSTRAINT votolaurea CHECK (((votolaurea > 0) AND (votolaurea < 111))) NOT VALID;


--
-- TOC entry 3272 (class 2620 OID 16481)
-- Name: appelli calendario; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER calendario BEFORE INSERT ON public.appelli FOR EACH ROW EXECUTE FUNCTION public.calendario();


--
-- TOC entry 3276 (class 2620 OID 16555)
-- Name: studente chek_prof; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER chek_prof BEFORE INSERT ON public.studente FOR EACH ROW EXECUTE FUNCTION public.check_prof();


--
-- TOC entry 3273 (class 2620 OID 16482)
-- Name: insegnamento creazione_insegnamento; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER creazione_insegnamento BEFORE INSERT ON public.insegnamento FOR EACH ROW EXECUTE FUNCTION public.creazione_insegnamento();


--
-- TOC entry 3275 (class 2620 OID 16483)
-- Name: provare insert_prova; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER insert_prova BEFORE INSERT ON public.provare FOR EACH ROW EXECUTE FUNCTION public.insert_prova();


--
-- TOC entry 3274 (class 2620 OID 16484)
-- Name: propedeutico propedeutico; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER propedeutico BEFORE INSERT ON public.propedeutico FOR EACH ROW EXECUTE FUNCTION public.propedeutico();


--
-- TOC entry 3277 (class 2620 OID 16485)
-- Name: studente sposta_studente; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER sposta_studente BEFORE DELETE ON public.studente FOR EACH ROW EXECUTE FUNCTION public.sposta_studente();


--
-- TOC entry 3257 (class 2606 OID 16486)
-- Name: appelli appelli_idinsegnamento_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appelli
    ADD CONSTRAINT appelli_idinsegnamento_fkey FOREIGN KEY (idinsegnamento) REFERENCES public.insegnamento(idinsegnamento);


--
-- TOC entry 3258 (class 2606 OID 16491)
-- Name: docente docente_idpersona_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT docente_idpersona_fkey FOREIGN KEY (idpersona) REFERENCES public.persona(idpersona);


--
-- TOC entry 3259 (class 2606 OID 16496)
-- Name: insegnamento insegnamento_codicedocente_idpersona_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.insegnamento
    ADD CONSTRAINT insegnamento_codicedocente_idpersona_fkey FOREIGN KEY (codicedocente, idpersona) REFERENCES public.docente(codicedocente, idpersona);


--
-- TOC entry 3260 (class 2606 OID 16501)
-- Name: insegnamento insegnamento_idcorso_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.insegnamento
    ADD CONSTRAINT insegnamento_idcorso_fkey FOREIGN KEY (idcorso) REFERENCES public.corso_di_laurea(idcorso);


--
-- TOC entry 3261 (class 2606 OID 16506)
-- Name: propedeutico propedeutico_idinsegnamento_diopendente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.propedeutico
    ADD CONSTRAINT propedeutico_idinsegnamento_diopendente_fkey FOREIGN KEY (idinsegnamento_dipendente) REFERENCES public.insegnamento(idinsegnamento);


--
-- TOC entry 3262 (class 2606 OID 16511)
-- Name: propedeutico propedeutico_idinsegnamento_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.propedeutico
    ADD CONSTRAINT propedeutico_idinsegnamento_fkey FOREIGN KEY (idinsegnamento) REFERENCES public.insegnamento(idinsegnamento);


--
-- TOC entry 3263 (class 2606 OID 16561)
-- Name: provare provare_idappello_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.provare
    ADD CONSTRAINT provare_idappello_fkey FOREIGN KEY (idappello) REFERENCES public.appelli(idappello) NOT VALID;


--
-- TOC entry 3264 (class 2606 OID 16516)
-- Name: provare provare_idpersona_matricola_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.provare
    ADD CONSTRAINT provare_idpersona_matricola_fkey FOREIGN KEY (idpersona, matricola) REFERENCES public.studente(idpersona, matricola);


--
-- TOC entry 3265 (class 2606 OID 16566)
-- Name: provare_storico provare_storico_idappello_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.provare_storico
    ADD CONSTRAINT provare_storico_idappello_fkey FOREIGN KEY (idappello) REFERENCES public.appelli(idappello) NOT VALID;


--
-- TOC entry 3266 (class 2606 OID 16521)
-- Name: provare_storico provare_storico_stu_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.provare_storico
    ADD CONSTRAINT provare_storico_stu_fkey FOREIGN KEY (matricola, idpersona) REFERENCES public.studente_storico(matricola, idpersona);


--
-- TOC entry 3267 (class 2606 OID 16526)
-- Name: segreteria segreteria_idpersona_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT segreteria_idpersona_fkey FOREIGN KEY (idpersona) REFERENCES public.persona(idpersona);


--
-- TOC entry 3268 (class 2606 OID 16531)
-- Name: studente studente_idcorso_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT studente_idcorso_fkey FOREIGN KEY (idcorso) REFERENCES public.corso_di_laurea(idcorso);


--
-- TOC entry 3269 (class 2606 OID 16536)
-- Name: studente studente_idpersona_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT studente_idpersona_fkey FOREIGN KEY (idpersona) REFERENCES public.persona(idpersona);


--
-- TOC entry 3270 (class 2606 OID 16541)
-- Name: studente_storico studente_storico_idcorso_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.studente_storico
    ADD CONSTRAINT studente_storico_idcorso_fkey FOREIGN KEY (idcorso) REFERENCES public.corso_di_laurea(idcorso);


--
-- TOC entry 3271 (class 2606 OID 16546)
-- Name: studente_storico studente_storico_idpersona_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.studente_storico
    ADD CONSTRAINT studente_storico_idpersona_fkey FOREIGN KEY (idpersona) REFERENCES public.persona(idpersona);


-- Completed on 2023-12-21 11:47:49

--
-- PostgreSQL database dump complete
--

