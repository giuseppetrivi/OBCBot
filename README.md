# OBC Bot - Orari Biglietteria COTRAP Telegram Bot

<img src="https://github.com/user-attachments/assets/40827a66-d55d-453f-af53-232fbb6286e7" alt="Description" width="120" height="120" style="margin-right: 20px;"> </br>

Lo scopo di questo bot Telegram è consentire la ricerca delle tratte coperte da aziende appartenenti a [COTRAP](https://www.cotrap.it/). Nello specifico, con questo bot è possibile eseguire le stesse operazioni di ricerca del [sito di COTRAP per la ricerca delle tratte](https://biglietteria.cotrap.it/#/ricerca), ma in modo più veloce e pratico dalla semplice interfaccia di bot Telegram </br>
Questo è un prodotto NON UFFICIALE, ovvero non è commissionato e mantenuto da COTRAP.

---
## 🛠️ Installazione e cenni su scelte progettuali
Per la realizzazione di questo bot ho usato il [framework StatefulBot](https://github.com/giuseppetrivi/StatefulBot-framework). </br>
Per installare il bot ed eseguirlo correttamente bisogna rispettare essenzialmente i requisiti indicati nel repo StatefulBot. </br>
Una volta clonato i repository con il comando `git clone https://github.com/giuseppetrivi/OBCBot.git`, bisogna importare il database MySQL contenente le tabelle per la gestione delle procedure di ricerca. Il file per l'importazione è contenuto nella cartella `database/`.

Ho utilizzato delle tabelle nel database anche per memorizzare tutte le informazioni statiche (relative alle località, alle aziende e ai poli), per evitare di dover eseguire una richiesta HTTP alle API ogni volta. In questo modo ho velocizzato la ricerca. Nello specifico la chiamata alle API viene fatta solamente nel momento in cui, impostati tutti i parametri, la ricerca viene avviata. Infatti è l'operazione che impiega più tempo per restituire i risultati e la risposta. </br>

In questo progetto ho seguito il coding style descritto nel framework ed ho usato l'inglese per la scrittura di tutte le parti di codice (commenti, variabili, classi, metodi, ecc...), mentre ho usato l'italiano nel database, per avere una corrispondenza 1:1 con i campi restituiti dalle chiamate agli endpoint delle API COTRAP, e qui, nel file README.

---
## 📝 Documentazione API COTRAP
Parto dalla premessa che queste API non sono pubbliche, bensì le ho individuate ispezionando il flusso di richieste fatte dal [sito di COTRAP per la ricerca delle tratte](https://biglietteria.cotrap.it/#/ricerca) durante una ricerca, appunto (per verificare apri il sito, apri "Ispeziona" e posizionati nel tab "Network", dopodiché inizia a selezionare i parametri di ricerca e nel flusso appariranno delle chiamate che restituiscono dati relativi alla ricerca). </br>
Inoltre ci sono anche altri endpoint, che però non ho documentato qui perché non li ho utilizzati all'interno di questo progetto.

Di seguito descriverò gli endpoint principale e i parametri essenziali.

### Link principale

[https://biglietteria.cotrap.it/api/ricerca](https://biglietteria.cotrap.it/api/ricerca)

Questo è il link per accedere a tutti gli endpoint della ricerca delle tratte COTRAP.

### Località urbane ed extraurbane

(urbane) ➝ [https://biglietteria.cotrap.it/api/ricerca/localitaurbane](https://biglietteria.cotrap.it/api/ricerca/localitaurbane)  
(extraurbane) ➝ [https://biglietteria.cotrap.it/api/ricerca/localitaextraurbane](https://biglietteria.cotrap.it/api/ricerca/localitaextraurbane)

Questi endpoint danno una lista delle località presenti nel database della COTRAP, con tutte le loro informazioni utili. Per ogni località sono indicati, ad esempio:
- il nome (`denominazione`)
- le località di arrivo (`localitaArrivo`), ovvero una lista di località raggiungibili da una data località
- le aziende (`aziende`), ovvero la lista di aziende che operano in una data località
- 
Le località urbane sono quelle in cui ci sono tratte interne allo stesso paese, quindi non hanno località di arrivo e, in generale, sono più povere di informazioni.

---
### Aziende di trasporto

[https://biglietteria.cotrap.it/api/ricerca/aziende](https://biglietteria.cotrap.it/api/ricerca/aziende)

Questo endpoint restituisce informazioni semplici (`id`, `denominazione` e `descrizione`) relative alle aziende di trasporto.

---
### Poli relativi ad un comune

[https://biglietteria.cotrap.it/api/ricerca/polilocalita/{idComune}](https://biglietteria.cotrap.it/api/ricerca/polilocalita/892)

I poli corrispondono alle fermate presenti in un determinato comune. Questo endpoint restituisce informazioni relative alle fermate, compresa l'azienda di trasporto che ne usufruisce.

---
### Ricerca biglietti per linea extraurbana

es. [https://biglietteria.cotrap.it/api/ricerca/extraurbana?idLocalitaPartenza=892\&idLocalitaArrivo=303\&idPoloPartenza=1491\&idPoloArrivo=1542\&dataPartenza=30/03/2027\&oraPartenza=16:00\&numeroCambi=0\&pagina=1](https://biglietteria.cotrap.it/api/ricerca/extraurbana?idLocalitaPartenza=892&idLocalitaArrivo=303&idPoloPartenza=1491&idPoloArrivo=1542&dataPartenza=30/03/2027&oraPartenza=16:00&numeroCambi=0&pagina=1)

Questo è l'endpoint per ottenere le informazioni relative agli orari per una specifica tratte scelta. Tutti i parametri della tratta (località di partenza e di arrivo, fermata di partenza e di arrivo, data e ora di ricerca) vanno indicati nella query del link. </br>
I parametri che bisogna indicare per questa richiesta, quindi, sono i seguenti:
- idLocalitaPartenza 
- idLocalitaArrivo 
- idPoloPartenza 
- idPoloArrivo 
- dataPartenza (in formato gg/mm/aaaa)
- oraPartenza (in formato hh:mm)
- numeroCambi, praticamente sempre = 0
- pagina, praticamente sempre = 1

---
### Ricerca biglietti per linea urbana

es. [https://biglietteria.cotrap.it/api/ricerca/urbana?idLocalita=268\&idPoloPartenza=3312\&idPoloArrivo=3274\&dataPartenza=30/03/2027\&oraPartenza=01:00\&pagina=1](https://biglietteria.cotrap.it/api/ricerca/urbana?idLocalita=268&idPoloPartenza=3312&idPoloArrivo=3274&dataPartenza=30/03/2027&oraPartenza=01:00&pagina=1)

La ricerca per una tratta urbana richiede le stesse informazioni della precedente eccetto per la località di arrivo, che in tal caso è omessa perché il trasporto è interno alla località indicata dal parametro `idLocalita`.

Gli attributi che bisogna indicare per questa richiesta sono i seguenti \=

* idLocalita (int)  
* idPoloPartenza (int)  
* idPoloArrivo (int)  
* dataPartenza (string date gg/mm/aaaa)  
* oraPartenza (string time hh:mm)  
* pagina (int)

