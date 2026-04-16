# Mall för PHP och MySQL
I denna mall är följande förinstallerat:
* PHP
* mysqli-tillägget till PHP
* Git - för versionshantering
* Composer för att installera externa PHP-paket
* Paketet `phpdotenv` för att läsa in hemliga värden till miljö-variabler (environmental variables). Detta för att på ett säkrare sätt lagra känslig data som lösenord. 

## Använda mallen 
* Välj `Use this template` uppe till höger. Välj `Create new repository`.
* Ge ditt projekt ett passande namn. (Inga mellanslag eller internationella tecken är tillåtna.)
* Välj `Create repository` för att skapa projektet.

## Börja koda på ditt projekt
* Välj `Code` → `Codespaces` → `Create codespace on main`
* Vänta medan den virtuella utvecklingsmiljön startar igång.
* I terminalfönstret i nederkant kör kommandot `composer install`. Detta installerar de externa kodbibliotek som används av projektet.

## Ange datasbas-info i filen .env
* Skapa en ny fil med namnet `.env` - i denna fil kan man ange hemliga värden till miljö-variabler.
* Lägg till två rader till filen där du anger ditt användarnamn och lösenord till ditt MySQL-konto:
```    
DB_USER=användarnamn
DB_PASS=lösenord
```

## Starta webservern
För att se se ditt webbprojekt behöver du starta PHPs inbyggda webserver: 
* Kör terminal-kommandot: ```php -S localhost:8000```
* Välj `Open in browser` i notifieringen i nederkant

## Spara förändringar
* Gå in på `Source control` till vänster i mitten. (Ikon med tre cirklar med linjer emellan.)
* För musen över `Changes` och tryck på pluset: `+`.
* Skriv ett meddelande som kortfattat beskriver de ändringar du gjort.
* Tryck på ```✓ Commit```
* Tryck på ```Sync Changes```
