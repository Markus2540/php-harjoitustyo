const littleImages = document.querySelector(".littleimages");
const passImages = document.getElementById("passimages");
const isoTuoteKuva = document.querySelector("#isotuotekuva");

/*Tuodaan sivulla olevasta piilotetusta tekstikentästä merkkijono ja muutetaan 
 * se taulukoksi*/
let kuvat = passImages.textContent.split(",");

function lisaaPikkukuvat() {    
    for (let i = 1; i < kuvat.length; i++) {
        const img = document.createElement("img");
        img.src = `kuvat/${kuvat[i]}`;
        img.style.maxWidth = "180px";
        img.style.maxHeight = "180px";
        img.addEventListener('click', () => {
            isoTuoteKuva.src = `kuvat/${kuvat[i]}`;
        });
        littleImages.appendChild(img);
    }
}
lisaaPikkukuvat();