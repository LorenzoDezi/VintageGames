/*
	questo javascript richiama una funzione periodica
	che aggiorna la sessione (e quindi i cookie). L'utilità
	principale sta nel fatto che se un utente gioca senza cambiare
	pagina la sessione resta comunque aggiornata(un altro utente non 
	può connettersi) e se la pagina rimane aperta e l'utente è inattivo
	per più di 20 minuti, allora si effettua il logout automatico per
	evitare un blocco dell'account.
*/ 
var idleTime = 0;
var idleInterval;
var connectionControl = function() {

	idleInterval = setInterval(timerIncrement, 60000);

	$(this).mousemove(function(e) {
		idleTime = 0;
	});

	$(this).keypress(function(e) {

		idleTime = 0;

	});



}
$(document).ready(connectionControl);

function timerIncrement() {

	idleTime++;
	$.get("updateSession.php");
	if(idleTime > 10) {
		//se l'utente rimane fermo per più di 10 minuti
		//non viene più aggiornata la sessione permettendo
		//la connessione da altri client
		window.clearInterval(idleInterval); 
	}
}