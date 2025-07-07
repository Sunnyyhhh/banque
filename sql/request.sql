--compter le nb de prets qui sont valides
select count(*) 
as total
from pret 
where statut = 1;

--compter le nb de prets qui sont non valides
select count(*) 
as total
from pret 
where statut = 0;