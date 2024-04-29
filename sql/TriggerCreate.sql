CREATE TRIGGER calendario 
BEFORE INSERT ON public.appelli 
FOR EACH ROW EXECUTE FUNCTION public.calendario();

CREATE TRIGGER chek_prof 
BEFORE INSERT ON public.studente 
FOR EACH ROW EXECUTE FUNCTION public.check_prof();

CREATE TRIGGER creazione_insegnamento 
BEFORE INSERT ON public.insegnamento 
FOR EACH ROW EXECUTE FUNCTION public.creazione_insegnamento();

CREATE TRIGGER insert_prova 
BEFORE INSERT ON public.provare 
FOR EACH ROW EXECUTE FUNCTION public.insert_prova();

CREATE TRIGGER propedeutico 
BEFORE INSERT ON public.propedeutico 
FOR EACH ROW EXECUTE FUNCTION public.propedeutico();

CREATE TRIGGER sposta_studente 
BEFORE DELETE ON public.studente 
FOR EACH ROW EXECUTE FUNCTION public.sposta_studente();
