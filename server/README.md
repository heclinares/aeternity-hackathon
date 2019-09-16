#Server example

Pretended to create a backend as a game publisher company and connect the login with Aeternity function with it.
After login, the user can download the games he owns. This part of the project is unfinished.

To connect PHP with the testnet, we've created a simple middleware in nodejs, as an easy way to call smart contract function game_balance from PHP
To test the middleware you can launch it using:
node server.js

Then, you can retrieve the game balance sending any address and contract you want, like this:
http://127.0.0.1:3000/gameBalance?userAddress=ak_2shWpEfCbQuahbrRWgkv7Mi2vCv4zov3Ybf6XiPRUZYjiVt5Wf&gameAddress=ct_cviBeBP3BTR7WoQ1qSbVK6j7oGBHsnHz1i783sLqEKfE9uurG

The project is far to be completed, but I've uploaded it because it's code developed during the Hackathon.
