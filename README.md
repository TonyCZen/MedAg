# 🗓️ MedAg — Gestionale Agenda Medica Full-Stack

**MedAg** è una web app full-stack sviluppata per ottimizzare e semplificare la gestione degli appuntamenti all'interno di uno studio medico o di professionisti sanitari. Il progetto è stato ideato e implementato per rispondere a esigenze reali di organizzazione, sicurezza dei dati e fluidità nell'esperienza utente.

---

### 📺 Demo & Anteprima

Ecco una breve dimostrazione video del funzionamento del gestionale (creazione appuntamenti e interazione asincrona):

https://github.com/TonyCZen/MedAg/blob/main/images/demo.mp4

#### Schermate dell'applicazione:

| Pagina di Acceso (Login) | Dashboard / Agenda |
|--- |--- |
| ![Login](images/login.png) | ![Dashboard](images/dashboard.png) |

| Lista Appuntamenti | Modulo Nuovo Appuntamento |
|--- |--- |
| ![Lista Appuntamenti](images/listaappuntamenti.png) | ![Nuovo Appuntamento](images/nuovoappuntamento.png) |

---

### 🚀 Funzionalità Principali

*   **Sistema di Autenticazione Completo:** Registrazione, login e recupero password nativi, completi di verifica dell'indirizzo email per garantire l'accesso ai soli utenti autorizzati.
*   **Gestione Appuntamenti (CRUD asincrono):** Possibilità di creare, visualizzare, modificare e cancellare gli appuntamenti in tempo reale senza dover ricaricare l'intera pagina del browser.
*   **Validazione Avanzata dei Dati:** Controllo rigoroso dei dati inseriti nei moduli (date, orari, formati testo) prima del salvataggio nel database.
*   **Sicurezza e Permessi:** Controllo degli accessi strutturato per fare in modo che ogni utente registrato possa visualizzare e modificare esclusivamente la propria agenda personale.

---

### 🛠️ Tecnologie & Architettura

Il progetto adotta un'architettura robusta e moderna, sfruttando i seguenti strumenti:

*   **Backend:** PHP con il framework **Laravel**, sfruttando le logiche MVC (Model-View-Controller) per una netta separazione dei ruoli all'interno del codice.
*   **Frontend & Interattività:** Layout responsive basati sulle viste **Blade** di Laravel, arricchiti da logiche asincrone tramite **Fetch API (AJAX)** per una navigazione fluida e dinamica.
*   **Database:** **MySQL**, gestito in modo sicuro tramite le **Migrations** per il controllo della struttura dei dati ed **Eloquent ORM** per le query.
*   **Ambiente di Sviluppo Locale:** Laragon e server Apache su ambiente Windows.

---

### 🔒 Dettagli Tecnici Rilevanti

Per dimostrare un approccio professionale allo sviluppo, nel codice sono stati implementati:
1.  **Laravel Policies:** Utilizzate per associare in sicurezza ogni record degli appuntamenti all'ID dell'utente proprietario, impedendo qualsiasi tentativo di accesso non autorizzato tramite URL.
2.  **Form Requests dedicate:** Spostamento della logica di validazione dai Controller a classi di richiesta specifiche, mantenendo il codice pulito, leggibile e facilmente mantenibile (principio di singola responsabilità).

---

### 📦 Come Avviare il Progetto Localmente

*(Nota per i recruiter: il progetto richiede un ambiente PHP/MySQL locale come Laragon o XAMPP).*

1. Clonare il repository sul proprio computer:
   ```bash
   git clone [https://github.com/TonyCZen/MedAg.git](https://github.com/TonyCZen/MedAg.git)

---

## ⚖️ Licenza e Proprietà Intellettuale

Ogni diritto sul codice, sul database e sulla logica applicativa di questo progetto è riservato a **Tony** (TonyCZen). 
Il software è pubblicato su GitHub esclusivamente a scopo di **portfolio didattico** per dimostrare le mie competenze tecniche ai fini di selezione professionale.

**Non è concessa alcuna autorizzazione** a copiare, duplicare, modificare, distribuire o utilizzare questo codice per scopi commerciali, progetti personali derivati o pubblicazioni a proprio nome senza l'esplicito consenso scritto dell'autore.