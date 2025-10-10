# Broken Images Scanner & Bulk Deleter

![Version](https://img.shields.io/badge/version-1.3.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)

Plugin WordPress professionale per scansionare la Libreria Media alla ricerca di immagini rotte (404, 410 o irraggiungibili) con funzionalità di eliminazione massiva.

**Sviluppato da:** DWAY Agency  
**Versione:** 1.3.0

---

## 📋 Indice

- [Caratteristiche](#-caratteristiche)
- [Requisiti](#-requisiti)
- [Installazione](#-installazione)
- [Utilizzo](#-utilizzo)
- [Funzionalità Tecniche](#-funzionalità-tecniche)
- [Parametri di Configurazione](#-parametri-di-configurazione)
- [FAQ](#-faq)
- [Troubleshooting](#-troubleshooting)
- [Changelog](#-changelog)
- [Licenza](#-licenza)

---

## ✨ Caratteristiche

### Funzionalità Principali

- ✅ **Scansione AJAX Progressiva**: Sistema non-bloccante che scansiona la libreria media in batch
- 📊 **Progress Bar in Tempo Reale**: Visualizzazione in tempo reale dell'avanzamento della scansione
- 🔍 **Rilevamento Intelligente**: Identifica immagini con status code 404, 410 o irraggiungibili
- 📅 **Filtri Avanzati**: Filtra le immagini per intervallo di date
- 🗑️ **Eliminazione Massiva**: Elimina tutte le immagini rotte con un solo clic
- 🎯 **Eliminazione Singola**: Possibilità di eliminare o modificare immagini individualmente
- ⚙️ **Configurazione Flessibile**: Parametri configurabili per batch size e limiti
- 🔒 **Sicurezza Integrata**: Nonce WordPress, capability checks e sanitizzazione input
- 💾 **Risultati Persistenti**: I risultati vengono salvati e sono consultabili anche dopo la scansione
- 🚫 **Annullamento Scansione**: Possibilità di interrompere la scansione in qualsiasi momento

### Vantaggi

- **Non-bloccante**: Non rallenta il sito durante la scansione
- **Scalabile**: Gestisce migliaia di immagini senza problemi
- **User-Friendly**: Interfaccia intuitiva in italiano
- **Efficiente**: Richieste HTTP ottimizzate con timeout configurabili
- **Affidabile**: Gestione errori completa e logging dettagliato

---

## 🔧 Requisiti

### Requisiti Minimi

- **WordPress**: 5.0 o superiore
- **PHP**: 7.4 o superiore
- **MySQL**: 5.6 o superiore
- **Permessi utente**: `upload_files` capability

### Requisiti Consigliati

- **WordPress**: 6.0+
- **PHP**: 8.0+
- **Memory Limit**: 128MB minimo (256MB consigliato)
- **Max Execution Time**: 60 secondi minimo
- **cURL** o **allow_url_fopen** abilitato per richieste HTTP

---

## 📥 Installazione

### Metodo 1: Installazione Manuale

1. Scarica i file del plugin
2. Crea una cartella `broken-images` in `/wp-content/plugins/`
3. Copia i file `broken-images.php` e `broken-images.js` nella cartella
4. Vai su **Plugin** nel pannello WordPress
5. Attiva **Broken Images Scanner & Bulk Deleter**

### Metodo 2: Upload via Dashboard

1. Comprimi i file del plugin in un file `broken-images.zip`
2. Vai su **Plugin > Aggiungi Nuovo**
3. Clicca su **Carica Plugin**
4. Seleziona il file `.zip` e clicca su **Installa Ora**
5. Attiva il plugin

### Verifica Installazione

Dopo l'attivazione, dovresti vedere una nuova voce **Broken Images** nel menu laterale di WordPress con l'icona di un'immagine.

---

## 🚀 Utilizzo

### 1. Accedere al Plugin

Dal pannello di amministrazione WordPress:
- Clicca su **Broken Images** nel menu laterale
- Si aprirà la pagina principale del plugin

### 2. Configurare la Scansione

#### Parametri Disponibili

**Data inizio (opzionale)**
- Filtra le immagini caricate dopo questa data
- Formato: selettore data HTML5
- Lascia vuoto per scansionare tutte le immagini

**Data fine (opzionale)**
- Filtra le immagini caricate prima di questa data
- Formato: selettore data HTML5
- Lascia vuoto per scansionare tutte le immagini

**Batch Size**
- Numero di immagini da controllare per ogni richiesta AJAX
- Intervallo: 10 - 500
- Default: 100
- **Consiglio**: Usa valori più bassi (50-100) se hai problemi di timeout

**Limite Massimo**
- Numero massimo totale di immagini da scansionare
- Intervallo: 100 - 10.000
- Default: 2.000
- **Consiglio**: Per librerie molto grandi, fai scansioni multiple con filtri data

### 3. Avviare la Scansione

1. Configura i parametri desiderati
2. Clicca su **Avvia Scansione**
3. Attendi il completamento (la progress bar si aggiornerà in tempo reale)
4. Non chiudere la pagina durante la scansione

#### Monitoraggio Progresso

Durante la scansione vedrai:
- **Progress Bar**: Percentuale completamento visiva
- **Controllati**: Numero di immagini già verificate
- **Rotti**: Numero di immagini rotte trovate fino a ora

### 4. Visualizzare i Risultati

Al termine della scansione, la pagina si ricaricherà automaticamente e mostrerà:

- **Data scansione**: Timestamp dell'ultima scansione
- **Immagini controllate**: Totale immagini verificate
- **Immagini rotte trovate**: Numero di immagini con problemi

#### Tabella Risultati

Per ogni immagine rotta troverai:
- **ID**: ID WordPress dell'attachment
- **Titolo**: Nome/titolo dell'immagine
- **URL**: Link diretto al file immagine
- **Status**: Codice di risposta HTTP (es. 404, 410, ERROR)
- **Azioni**: Pulsanti per modificare o eliminare singolarmente

### 5. Eliminare le Immagini Rotte

#### Eliminazione Massiva

1. Clicca su **Elimina Tutte (N)** sopra la tabella
2. Conferma l'operazione nel popup di conferma
3. Tutte le immagini rotte verranno eliminate definitivamente

⚠️ **ATTENZIONE**: Questa azione è **irreversibile**!

#### Eliminazione Singola

1. Clicca su **Elimina** nella riga dell'immagine specifica
2. Conferma l'operazione
3. Solo quella immagine verrà eliminata

### 6. Annullare una Scansione

Durante una scansione in corso:
1. Clicca sul pulsante **Annulla**
2. La scansione si interromperà al completamento del batch corrente
3. I risultati parziali verranno comunque salvati

---

## 🔬 Funzionalità Tecniche

### Architettura

#### Backend (PHP)

**Classe Principale**: `Broken_Images_Scanner`

**Hook WordPress**:
- `admin_menu` - Registra la pagina di amministrazione
- `admin_enqueue_scripts` - Carica asset JS/CSS
- `wp_ajax_broken_images_scan_step` - Gestisce scansione AJAX
- `admin_post_broken_images_delete` - Gestisce eliminazione massiva

**Metodi Principali**:

```php
ajax_scan_step()
```
- Esegue un batch di controlli sulle immagini
- Utilizza WP_Query con paginazione
- Salva risultati progressivi in `wp_options`
- Ritorna JSON con stato avanzamento

```php
check_image_url( $url )
```
- Verifica accessibilità URL con `wp_remote_head()`
- Timeout: 10 secondi
- Redirection: max 5
- SSL verify: disabilitato (per compatibilità)
- Ritorna: codice HTTP o messaggio errore

```php
handle_delete()
```
- Elimina definitivamente gli attachment con `wp_delete_attachment()`
- Include file allegati al media
- Reset risultati dopo eliminazione

#### Frontend (JavaScript)

**Gestione Scansione Asincrona**:
- Loop ricorsivo con `async/await`
- Fetch API per richieste AJAX
- Aggiornamento UI in tempo reale
- Gestione errori e timeout

**Funzioni Principali**:
```javascript
loop() // Loop asincrono principale
setProgress(percentage) // Aggiorna progress bar
setMeta(text) // Aggiorna testo informativo
disable(boolean) // Blocca/sblocca form
```

### Sicurezza

#### Verifica Permessi
```php
current_user_can( 'upload_files' )
```

#### Nonce Validation
- AJAX: `check_ajax_referer()`
- Form POST: `check_admin_referer()`

#### Sanitizzazione Input
- `sanitize_text_field()` per date
- `intval()` per numeri
- `esc_url()`, `esc_html()`, `esc_attr()` per output

### Performance

#### Ottimizzazioni
- Query con `fields => 'ids'` (solo ID, non oggetti completi)
- `wp_remote_head()` invece di GET (solo header, non body)
- Batch processing per evitare timeout
- Transient caching per risultati

#### Limiti Configurabili
- Batch size: previene timeout PHP
- Limite massimo: previene sovraccarico server
- Timeout HTTP: 10 secondi per richiesta

---

## ⚙️ Parametri di Configurazione

### Costanti Personalizzabili

Puoi modificare questi valori nel file `broken-images.php`:

```php
// Timeout per richieste HTTP (secondi)
const HTTP_TIMEOUT = 10;

// Numero massimo di redirect da seguire
const HTTP_REDIRECTS = 5;

// Verifica SSL (true/false)
const HTTP_SSL_VERIFY = false;
```

### Filtri WordPress

Il plugin supporta questi filtri per sviluppatori:

```php
// Modifica argomenti query immagini
add_filter('broken_images_query_args', function($args) {
    $args['posts_per_page'] = 50; // Custom batch size
    return $args;
});

// Modifica timeout HTTP
add_filter('broken_images_http_timeout', function($timeout) {
    return 15; // 15 secondi
});

// Modifica status codes considerati "rotti"
add_filter('broken_images_is_broken', function($is_broken, $status) {
    // Considera rotto anche status 403
    return in_array($status, [403, 404, 410]);
}, 10, 2);
```

---

## ❓ FAQ

### 1. Quanto tempo impiega una scansione?

Dipende dal numero di immagini:
- **100 immagini**: ~1-2 minuti
- **1.000 immagini**: ~10-15 minuti
- **5.000 immagini**: ~40-60 minuti

### 2. Il plugin rallenta il mio sito?

No, la scansione è **completamente asincrona** e non influisce sulle performance del sito per i visitatori.

### 3. Posso recuperare le immagini eliminate?

No, l'eliminazione è **permanente**. Assicurati di avere un backup prima di procedere.

### 4. Cosa succede se chiudo la pagina durante la scansione?

La scansione si interromperà, ma i risultati parziali saranno salvati. Puoi riavviare la scansione in qualsiasi momento.

### 5. Quali status code vengono considerati "rotti"?

Tutti gli status code diversi da **200** (OK), inclusi:
- **404**: Not Found
- **410**: Gone
- **403**: Forbidden
- **500**: Server Error
- **Errori di timeout/connessione**

### 6. Il plugin funziona con CDN?

Sì, il plugin controlla l'URL finale dell'immagine, che può essere su un CDN.

### 7. Posso scansionare solo immagini recenti?

Sì, usa i filtri **Data inizio** e **Data fine** per limitare la scansione a intervalli specifici.

### 8. Il plugin elimina anche le miniature?

Sì, `wp_delete_attachment($id, true)` elimina l'attachment e **tutte** le sue dimensioni/thumbnail associate.

### 9. Viene fatto un backup automatico?

No, il plugin non crea backup. Si consiglia di usare un plugin di backup o fare backup manuale prima di eliminazioni massive.

### 10. Posso esportare i risultati?

Attualmente no, ma puoi copiare la tabella o aggiungere questa funzionalità personalizzando il plugin.

---

## 🔧 Troubleshooting

### Problema: Scansione si blocca o timeout

**Soluzione**:
1. Riduci il **Batch Size** a 50 o meno
2. Aumenta `max_execution_time` in `php.ini`:
   ```ini
   max_execution_time = 300
   ```
3. Aumenta `memory_limit`:
   ```ini
   memory_limit = 256M
   ```

### Problema: Errore "Permessi insufficienti"

**Soluzione**:
- Assicurati che l'utente abbia il ruolo di **Editor** o superiore
- Verifica che l'utente abbia la capability `upload_files`

### Problema: Immagini non vengono trovate

**Soluzione**:
1. Verifica che le immagini siano di tipo `post_type: attachment`
2. Controlla che `post_mime_type` sia un tipo immagine (image/*)
3. Verifica connessione internet del server

### Problema: False positivi (immagini funzionanti segnalate come rotte)

**Soluzione**:
- Alcune immagini potrebbero avere protezioni anti-hotlinking
- Immagini su domini esterni potrebbero bloccare richieste HEAD
- Verifica manualmente prima di eliminare

### Problema: Errore JavaScript in console

**Soluzione**:
1. Pulisci cache browser
2. Disattiva altri plugin che potrebbero causare conflitti
3. Verifica compatibilità tema

### Debug Mode

Abilita debug WordPress per vedere errori dettagliati:

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

I log saranno in `/wp-content/debug.log`

---

## 📝 Changelog

### Version 1.3.0 (Corrente)
- ✅ Implementazione completa funzionalità AJAX
- ✅ Aggiunta gestione errori migliorata
- ✅ Fix break level in loop controlli
- ✅ UI ottimizzata con progress bar
- ✅ Sanitizzazione e sicurezza completa
- ✅ Tabella risultati con azioni individuali
- ✅ Messaggi di conferma eliminazione

### Version 1.2.0
- Aggiunta funzionalità filtri data
- Migliorata gestione batch size

### Version 1.1.0
- Prima release pubblica
- Funzionalità base scansione

### Version 1.0.0
- Release iniziale interna

---

## 🛠️ Sviluppo e Contributi

### Struttura File

```
broken-images/
├── broken-images.php    # Plugin principale (backend)
├── broken-images.js     # Script frontend (AJAX)
└── README.md           # Documentazione
```

### Codice di Qualità

Il plugin segue:
- **WordPress Coding Standards**
- **PHP PSR-12** per formattazione
- **ES6+** per JavaScript
- **Security best practices** WordPress

### Testing

Prima di ogni release:
1. Test su PHP 7.4, 8.0, 8.1, 8.2
2. Test su WordPress 5.0, 5.9, 6.0+
3. Test con temi: Twenty Twenty-One, Twenty Twenty-Three
4. Test con 100, 1.000, 5.000+ immagini
5. Test con/senza CDN
6. Security scan con WPScan

---

## 📄 Licenza

Questo plugin è rilasciato sotto **GPL v2 o successiva**.

```
Copyright (C) 2024 DWAY Agency

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

## 👥 Crediti

**Sviluppato da**: DWAY Agency  
**Versione**: 1.3.0  
**Data Release**: 2024

### Tecnologie Utilizzate

- WordPress Core API
- jQuery
- Fetch API
- WP_Query
- WordPress HTTP API

---

## 📞 Supporto

Per segnalazioni bug, richieste funzionalità o domande:

- **Email**: support@dwayagency.com
- **Website**: https://dwayagency.com

---

## ⚠️ Disclaimer

L'eliminazione di file media è **permanente e irreversibile**.  
Effettua sempre un **backup completo** prima di utilizzare la funzione di eliminazione massiva.

L'autore e DWAY Agency non sono responsabili per perdite di dati dovute all'uso improprio del plugin.

---

**Grazie per aver scelto Broken Images Scanner & Bulk Deleter!** 🎉

Se il plugin ti è utile, lascia una recensione e condividilo con la community WordPress italiana!

