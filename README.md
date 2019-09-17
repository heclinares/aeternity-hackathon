# GAMETERNITY PROJECT

Project developed for the AETERNITY HACKATHON, Prague 2019:
[Haeckathon](https://aeternityuniverse.com/haeckathon)

Link to the presentation document:
[Presentation](https://github.com/heclinares/aeternity-hackathon/blob/master/doc/aeternity-hackathon.pdf)

## ABOUT GAMETERNITY
Our goal is to adapt the video game industry to Blockchain technology. Games industry relays in lot of processes than can be adapted to Blockchain, making them fairer, efficient and specially, more transparent. Having so many possibilities, for this hackathon we have focused on:

    Proof of concept of a decentralized marketplace for videogames
        Now you can buy games using Waellet!
    Tested some processes to create a login process for non-blockchain backends, using private/public key signature and random generated tokens
    First version of an eSports tournament record smart contract

Warning: old game contract version has a bug in the buyGame function, to test this function, deploy a new game or use the last one published, that was uploaded after the fix

## INSTALL
To test the system, install the frontend:

cd gameternity-vue

yarn

yarn run dev

## CONTRACTS
GAME: create a new contract that represents a video game. This contracts use part of the token behaviour, to track the users that currently own the game. It includes information like title, url, image, price, etc. and some useful functions like buyGame and game_balance

SHOP: the system provides a pre-deployed shop contract (testnet). The address is included in the Frontend.
The shop contract keeps a record of GAME contracts, to easily act as a logical entry point for the market place
Added some helper functions and data structures.

TOURNAMENT: we've included a early version of a tournament contract, to record eSports match results. This contract is not connected to the Frontend, but helps to understand the global idea around Gameternity

The process to upload a game to the shop is simple:
  1. - First deploy a GAME contract
  2. - Use the deployed GAME address to call SHOP.addGame method
  3. - Check SHOP.getGames


