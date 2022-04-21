# PHP-harjoitustyo

## Projektin kuvaus

Tämän projektin tarkoituksena oli opetella web-ohjelmointia käyttäen PHP:tä, JavaScriptiä, tietokantakielenä MySql:ää (MariaDB xamppissa) ja perus-html:ää ja muotoilua css:n avulla. Päätavoite oli saada toiminnot toimimaan halutulla tavalla, joten värisuunnittelussa on käytetty kaupan puolella suunnittelua helpottavia divikohtaisia taustavärejä ja joidenkin sivujen sisältö on joko tyhjä tai testisisältöä. Screenshots-kansiossa on muutama kuvakaappaus sivusta.

Tällä sivustolla käyttäjä voi lisätä ja poistaa tuotteita ostoskorista sekä kirjautuneena että kirjautumattomana, tehdä tilin ja muokata tilin tiedoista salasanaa ja osoitetietoja.

Tuotteiden selaaminen tapahtuu joko navigointipalkissa olevien linkkien kautta, etsi-kentän ehdotusten tai tuotteiden etsimissivuston kautta. Tuotteita voidaan lisätä ostoskoriin maksimissaan vain sen verran, mitä varastossa on. Lisättäessä tuotteita ostoskoriin ostoskorille luodaan oma id tietokantaan johon lisätään asnro sen mukaan, onko käyttäjä kirjautuneena tililleen vai ei. Kirjautumaton käyttäjä voi kirjautua myös sisään ostoskorin luotuaan, jolloin ostoskorin tietoihin päivitetään asnro.

Admin-puolella tuotteita voidaan lisätä ja muokata. Tuotteen lisäyksen aikana tuotteeseen voidaan myös liittää jpeg-muotoisia kuvia tiettyjen ehtojen mukaan.

## Ulkoasu ja käyttäjäkokemus

Ulkoasultaan kauppapuoli on eri näyttökokoihin sopeutuva. Admin-puoli on suunniteltu siten, että sitä käytetään pöytäkoneen näytön kokoisella laitteella, eikä mobiililaitteella, kuten matkapuhelimella.

Käyttäjäkokemuksen kannalta suurin osa syötteitä vastaanottavista kentistä antaa jonkinlaisen palautteen kun käyttäjä koittaa syöttää vääräänmuotoista tietoa. Pisimmälle vietynä käyttäjän neuvominen on viety admin-puolen tuotteiden lisäämisen ja tuotteiden muokkaamisen sivustoilla, jossa käytetään html:ää ja JavaScriptiä syötteiden tarkistuksessa. Tällä tavalla voidaan myös välttää ylimääräisiä palvelintarkistuksia.

## Tietoturva

Tätä harjoitustyötä tehdessäni kiinnitin huomiota tietorurvaan liittyviin asioihin. Käyttäjäsyöte tarkistetaan PHP:n toimesta erilaisia säännöllisiä lausekkeita hyödyntäen. Toisinaan ne on liiankin rajoittavia, mutta harjoitusmielessä ne käy. 

Syötteen oikeellisuuden varmistamisen jälkeen tiedot viedään tietokantaan esivalmisteltujen lausekkeiden avulla. Tietokantayhteydessä käytin aluksi mysqliä, mutta lopulta siirryin käyttämään PDO:ta.

Olen myös yrittänyt miettiä miten käyttäjä voi yrittää väärinkäyttää lomakkeita ja tehdä niitä vastaan varmistavia toimenpiteitä. Esimerkiksi ostoskoriin käyttäjällä ei pitäisi pystyä lisäämään varastotilannetta enempää tuotteita, myynnistä poistettuja tuotteita tai sellaisia, joilla myynti ei ole vielä edes alkanut.

Tiliä luodessaan tai salasanaa vaihtaessaan salasanalla on tiettyjä monimutkaisuusvaatimuksia. Työntekijäpuolen sisäänkirjautuminen on suojattu salasanan arvaamisyrityksiä vastaan, mutta asiakaspuolen sisäänkirjautumistoimintoa en ole samalla tavalla suojannut.

## Sivuston toimintaan saattaminen

Tämän sivuston saa kokeilumielessä toimimaan xamppin kanssa helposti kopioimalla tietotekniikkakauppa-kansion sisältöineen xamppin htdocs-kansioon. Jos xampp on asennettu polkuun c:\xampp, tuotekuvien lisäämisen pitäisi toimia ilman muutoksia, mutta jos xampp on asennettu johonkin muuhun polkuun, niin tuotteidenlisaaminen.php tiedostossa pitää muokata $uploaddir-variaabelia. Tietokannan voi luoda tietokanta.sql-tiedoston komennot suorittamalla esim phpMyAdminissa.

Admin-puolelle ei ole omaa tilinluontilomaketta, vaan tili pitää ensin tehdä kauppapuolen tilinluontitoiminnolla ja sen perusteella tili pitää kopioida asiakas-taulukosta henkilokunta-taulukkoon käyttämällä (phpMyAdminissa) tietokantalausetta: 
```
INSERT INTO henkilokunta (etunimi, sukunimi, kayttajatunnus, salasana, lahiosoite, postinumero, postitoimipaikka, tili_luotu) SELECT etunimi, sukunimi, kayttajatunnus, salasana, lahiosoite, postinumero, postitoimipaikka, tili_luotu FROM asiakas WHERE asnro = [halutun tilin numero];
```

## Tämän projektin jatko

Tässä vaiheessa uusien ominaisuuksien lisääminen saa riittää. Tähän mennessä lisätyt toiminnot toimivat kuten pitääkin, vaikkakin joitakin voisi vielä parannella, kuten sisennyksiä, PHP-koodin sijaintia ja Admin-puolen headin toisto. Tässä vaiheessa jätän tämän vielä tällaiseksi ja alan tutkimaan uusia asioita. Tämän projektin osalta suunnitelmanani on tutkia virtuaalipalvelimelle asennetun LAMP-serverin toimintaa. Tämän lisäksi alan opiskelemaan jotakin uutta, todennäköisesti Reactia.