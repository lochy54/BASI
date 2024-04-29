--LAUREA--

CREATE FUNCTION public.laurea(mat character varying, voto integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
begin
 delete from studente where matricola=mat ;
 UPDATE studente_storico
	SET votolaurea=voto, ritirato=false
	WHERE matricola=mat;  
end;