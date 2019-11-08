# GAMETERNITY VUE FRONTEND

Frontend to connect to the Shop contract.

## INSTALL
Execute:

yarn

yarn run dev

## VIEWS
HOME: get game list from the contract, shows the list of games. Search bar not entirely working

REGISTER GAME: you can register your own game into the AEternity blockchain, you'll need to provide:
    - Title
    - Download URL
    - Game image
    - Description
    - Price (in AE)
After successful game register, you'll be redirected to HOME

LOGIN: connects to a server and ask for a login token. Signs the token using the Private Key.
    - Further versions should send the signed token
    - You can test using the PHP server provided in the project

We recommend to open the console log to check the steps the system is doing
