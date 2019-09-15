# GAMETERNITY PROJECT

Proof of concept of a decentralized marketplace for videogames. Tested some processes to create a login process for non-blockchain backends, using private/public key signature and random generated tokens.

## INSTALL
To test the system, install the frontend:
cd gameternity-vue
yarn
yarn run dev

## CONTRACTS
GAME: create a new contract that represents a video game. This contracts use part of the token behaviour, to track the users that currently own the game. It includes information like title, url, image, price, etc. and some useful functions like buyGame

SHOP: the system provides a pre-deployed shop contract (testnet). The address is included in the Frontend.
The shop contract keeps a record of GAME contracts, to easily act as a logical entry point for the market place.
Added some helper functions and data structures.

The process to upload a game to the shop is simple:
  1. - First deploy a GAME contract
  2. - Use the deployed GAME address to call SHOP.addGame method
  3. - Check SHOP.getGames


