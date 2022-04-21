/*
Tämä ei näytä olevan käytössä missään. 
*/

const btnIlmoituksenKuittaus = document.getElementById("btnilmoituksenkuittaus");
const tummennus = document.getElementById("tummennus");
const ilmoitusTummennuksenKeskella = document.getElementById("ilmoitustummennuksenkeskella");
const ilmoituksenTeksti = document.getElementById("ilmoituksenteksti");

function hideNotification() {
    tummennus.style.display = "none";
    ilmoitusTummennuksenKeskella.style.display = "none";
}
btnIlmoituksenKuittaus.addEventListener('click', hideNotification);

function showNotification() {
    tummennus.style.display = "block";
    ilmoitusTummennuksenKeskella.style.display = "block";
}