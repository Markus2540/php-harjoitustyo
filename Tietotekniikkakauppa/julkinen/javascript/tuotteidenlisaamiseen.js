/*
Tämä ei näytä olevan käytössä missään. Toiminto varmaan siirrettiin toisaalle.
*/

const lyhytKuvaus = document.getElementById('lyhytkuvaus');
const lyhyenKuvauksenPituus = document.getElementById('lyhyenkuvauksenpituus');

lyhytKuvaus.addEventListener('keyup', () => {
    lyhyenKuvauksenPituus.textContent = `${lyhytKuvaus.value.length}/400 `;
});