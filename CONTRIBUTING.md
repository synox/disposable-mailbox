:+1::tada: First off, thanks for taking the time to contribute! :tada::+1:

See the following guide on how to create a fork and a pull request: 
https://help.github.com/articles/fork-a-repo/
 

## Priciples in this project:
 * *less code, less bugs:* use fewer lines if possible, remove abstractions that are not required. 
 * *minimal features and complexity:* only implement features that are really necessary for most users 
 * *batteries included/No build process:* the user should only download the repo and update the config file in order to run the app.   
 * *no database:* the application does not have it's own database. The IMAP server is the database. 
 
 
 ## Standards
 We try to follow the following standards:
  * https://github.com/php-pds/skeleton
  * http://keepachangelog.com
  * 
  
## Do before pull request
 * run code style fixer (https://cs.symfony.com/)
   * `brew install php-cs-fixer`
   * `php-cs-fixer fix`
 