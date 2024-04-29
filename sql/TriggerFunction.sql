--CALENDARIO--

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

--CHECK PROF--

BEGIN
   if exists (select * from corso_di_laurea as c inner join insegnamento as i ON i.idcorso = c.idcorso inner join persona as p on p.idpersona = i.idpersona
	where p.idpersona=new.idpersona and c.idcorso= new.idcorso)
	then RAISE EXCEPTION 'IL PROFESSORE INSEGNA IN QUESTO CORSO';
	END IF;
RETURN NEW;
END;

--CREAZIONE INSEGNAMENTO--

    DECLARE
        insegnamenti_attivi integer;
    BEGIN

        SELECT COUNT(*) INTO insegnamenti_attivi
        FROM public.insegnamento
        WHERE codicedocente = new.codicedocente;

        IF  insegnamenti_attivi=3 THEN
            RAISE EXCEPTION 'Il docente ha già 3 insegnamenti attivi %',new.codicedocente ;
        END IF;

        RETURN NEW;
    END;

--INSERT PROVA--

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

--PROPEDEUTICO--

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

--SPOSTA STUDENTE--

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

