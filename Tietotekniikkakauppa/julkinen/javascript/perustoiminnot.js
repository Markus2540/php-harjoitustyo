let showCategoriesBtn = document.getElementById("showcategories");
let categories = document.getElementById("categories");
let dropDownContent = document.getElementsByClassName("dropdowncontent");
let showLoginAndCartBtn = document.getElementById("showloginandcart");
let kirjautumisIkkuna = document.getElementById("kirjautumisikkuna");


categories.style.display = "none";
/*
 * Seuraava funktio näyttää tai piilottaa Navigointi-ikkunan.
 */
function expandSelection() {
    (categories.style.display === "none") ? categories.style.display = "block" : 
            categories.style.display = "none";
}
showCategoriesBtn.addEventListener('click', expandSelection);

kirjautumisIkkuna.style.display = "none";
/*
 * Seuraava funktio näyttää tai piilottaa Tili/kori-ikkunan.
 */
function expandLoginAndCart() {
    (kirjautumisIkkuna.style.display === "none") ? kirjautumisIkkuna.style.display = 
            "block" : kirjautumisIkkuna.style.display = "none";
}
showLoginAndCartBtn.addEventListener('click', expandLoginAndCart);

/*
 *Lisätään sivulle tapahtumien kuuntelija, joka piilottaa Navigointi- ja 
 *Tili/kori-ikkunat, ellei klikkauksen kohde ole jokin ennalta määritetty kohde.
 */
window.onclick = function(event) {
    if (!(event.target.matches("#showcategories") || event.target.matches
    ("#showloginandcart") || event.target.matches(".logincontent"))) {
        categories.style.display = "none";
        kirjautumisIkkuna.style.display = "none";
    }
};


//Funktio dokumentin otsikon muuttamiseksi.
function setTitle (newTitle){
    document.title = newTitle;
}

//Funktio dokumentin kuvauksen muuttamiseksi.
function setDescription (newDescription) {
    document.getElementsByTagName('meta')["description"].content = newDescription;
}

const search = document.getElementById("search");
const ehdotukset = document.getElementById("ehdotukset");
const ehdotuslista = document.getElementById("ehdotuslista");

/*
 * Lisätään hakukenttään tapahtumien kuuntelija, joka reagoi näppäimen painalluksen
 * loppumiseen. Kutsuu liveSearch-funktiota tekstin pituuden ollessa 2 merkkiä 
 * tai enemmän, muuten piilottaa div id ehdotukset.
 */
search.addEventListener('keyup', () => {
    let searchTerm = search.value.trim();
    if (searchTerm.length > 1) {
        liveSearch(searchTerm);
    } else {
        ehdotukset.style.display = "none";
    }
});

/*
 * Lisätään hakukenttään tapahtumien kuuntelija, joka reagoi hakukentän menettäessä 
 * fokuksen. setTimeout-metodi piilottaa div id ehdotukset 100 millisekuntin
 * kuluttua, sillä ilman pientä viivettä linkin painaminen ei onnistuisi.
 * Toimintoa ei ole testattu kosketusnäyttöisellä laitteella.
 */
search.addEventListener('focusout', () => {
    setTimeout(() => {ehdotukset.style.display = "none";}, 100);
});

/*
 * liveSearch-funktio lähettää ajax.php-sivustolle hakukenttään kirjoitetun 
 * tekstin ja saa vastaukseksi JSON-dataa, jonka avulla ehdotustulokset rakennetaan.
 * Onnistuessaan lähettää tulokset naytaEhdotukset-funktioon.
 */
function liveSearch(searchTerm) {
    fetch('../moduulit/ajax.php', {
        method: 'POST',
        body: new URLSearchParams('search=' + searchTerm)
    })
        .then(searchResult => searchResult.json())
        .then(searchResult => naytaEhdotukset(searchResult))
        .catch(e => console.error('Error: ' + e));
}

/*
 * Kutsuttuna muuttaa div id ehdotukset näkyväksi, tyhjentää siinä olevan ehdotuslistan,
 * ja lähettää jokaisen taulukon rivin addRow-funktioon.
 */
function naytaEhdotukset(searchResult) {
    ehdotukset.style.display = "block";
    while (ehdotuslista.firstChild) {
        ehdotuslista.removeChild(ehdotuslista.firstChild);
    }
    if (searchResult.length === 0) {
        addErrorRow("Hakutuloksia ei löytynyt");
    } else if (searchResult === "Käytä hakukentässä vain kirjaimia, numeroita ja välilyöntejä.") {
        addErrorRow(searchResult);
    } else {
        searchResult.forEach(tulos => addRow(tulos));
    }
}

/*
 * Lisää ehdotuslistaan saamansa tietojen perusteella listaelementin, jossa on 
 * linkki. Näyttää tuotteen varasto- ja myymäläsaatavuuden hiiren osoittimen ollessa
 * päällä.
 */
function addRow(tulos) {
    const linkki = document.createElement("a");
    const li = document.createElement("li");
    let linkkiteksti = document.createTextNode(`${tulos.tuotenimi}`);
    linkki.appendChild(linkkiteksti);
    linkki.title = `Varastossa: ${tulos.varastossa}, myymälässä: ${tulos.myymalassa}.`;
    linkki.href = `tuote.php?id=${tulos.tuotenumero}`;
    li.appendChild(linkki);
    ehdotuslista.appendChild(li);
}
/*
 * Lisätään funktio tyhjän hakutuloksen tai muun virheen ilmaisemiseksi.
 */
function addErrorRow(message) {
    const li = document.createElement("li");
    li.textContent = `${message}`;
    ehdotuslista.appendChild(li);
}