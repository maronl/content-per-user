# Content per User 
WordPress plugin to allow adminstrators define if a post, page or custom post is available for all users or only for specific users (defined by username)


## done
10/05/2015
implemetare gestione dati reali per i contenuti di uno specifico utente (database schema, ajax return values)
 - ricerca reale json con ajax dei post da suggerire
 - salvataggio associazione user_id post_id quando faccio aggiungi
 - rimozione associazione user_id post_id quando faccio rimuovi
implementata lista reale dei contenuti di un utente nel prodiilo utente
implementato check prima di renderizzare il contenuto di un post per vedere se l'utente può o meno visualizzarlo
implementare possibilità di customizzare "messaggio di contenuto riservato" creando il file cpu-access-forbidden.php nella root del tema

09/05/2015
aggiungere sezione nel profilo utente per aggiungere i contenuti di un utente
implemetare template aggiunta contenuti ad un utente
implemetere template rimozione contenuti ad un utente
creato db schema per memorizzare relazione post_id <-> user_id e integrato nel plugin. lo schema viene inizializzato all'avvio del plugin


08/05/2015
creato plugin
gestione configurazione plugin nelle impostazioni di wordpress con pagina specifica
possibile configurare su quali post_type applicare la protezione del contenuto
aggiunto metabox content per user nella pagina di editing del post

## to be done
implementare template per messaggio "contenuto riservato"
