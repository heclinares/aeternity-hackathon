<template>
  <div>
    <!-- TOP IMAGE SECTION 'BANNER' -->
    <div class="banner" style="position: relative; top: 0; z-index: 0;">
      <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" style="position: relative !important;">
        <div class="carousel-inner" role="listbox" style="overflow: visible">
          <div class="item active home-bg" :style="style">
            <div class="clearfix"></div>
            <div class="carousel-caption banner-slider-inner banner-top-align col-sm-10" style="position: relative; margin-top: 4%">
              <div class="top-content-container text-left" style="margin-left: 10%">
                <!-- TITLE, SUBTITLE ZONE -->
                <h1 style="color: #fff; text-shadow: none; text-align: center;" data-animation="animated fadeInDown delay-05s">HOW IT WORKS</h1>
                <div class="row" style="margin-top: 100px;">
                  <div class="col-12 col-md-6">
                    <h2 style="color: #fff; text-shadow: none;" class="text-center">For GAMERS</h2>
                    <iframe width="100%" height="315" src="https://www.youtube.com/embed/CHgZ2YQOgso" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                  </div>
                  <div class="col-12 col-md-6">
                    <h2 style="color: #fff; text-shadow: none;" class="text-center">For DEVELOPERS</h2>
                    <iframe width="100%" height="315" src="https://www.youtube.com/embed/3SH3CPJhthE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Banner end -->
    
    <div class="clearfix"></div>
  
    <!-- GAME LIST ZONE -->
    <div id="intro" class="content-area featured-games" style="background: black">
      <div class="container">
        <!-- Main title -->
        <div class="row">
          <div class="col-md-12 text-center" style="color: #fff">
            <h2 style="border-bottom: 1px solid white; padding-bottom: 10px;">Publish or buy your games in this decentralized marketplace</h2>
            <div class="font-size-16px">No matter if you are a big publisher or a small indie developer, everyone have the same opportunities in GametribeZ</div>
            <div class="spacer clear " style="height:50px;"></div>
            <div class="h1-separator medium-blue"></div>
          </div>
        </div>
        <div class="row games-container">
          <div class="col-md-6" v-for="game in games">
            <div class="text-center game-card width-100">
              <div class="game-name">
                {{ game.name }} <br><small class="text-uppercase"></small>
              </div>
              <img alt="" :src="game.img" class="lazyload">
            </div>
            <div @click="tryBuy(game)" style="cursor: pointer; z-index: 2;width: 150px; height: 50px; position: absolute; left: 20px; bottom: 20px; color: #eee; font-size: 15px; background: rgba(100,100,100,0.5); border-radius: 24px;">
              <img src="/static/img/Aeternity.svg" style="width: 50px"> {{ game.price }} AE
            </div>
            <a :href="game.url" :title="game.name" class="" v-if="game.balance > 0">
              <div style="cursor: pointer; z-index: 2;width: 170px; padding: 10px; height: 50px; position: absolute; right: 20px; bottom: 20px; color: #eee; font-size: 15px; background: rgba(100,100,100,0.5); border-radius: 2px;">
                <img src="/static/img/icons8-checked-32.png" /> In your library
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import Vue from 'vue'
  import { getGameList, gameContract, call, setWaellet, isWaellet, buyGame, getUserAddress } from '@/Common/contract'

  export default {
    name: 'home',
    data: () => ({
      games: [],
      search: '',
      showResults: false
    }),
    computed: {
      style () {
        return 'background-image: /*url(static/img/bg_home.jpg);*/ background-position-x: -00px; background-color: #000;'
      }
    },
    methods: {
      tryBuy: function (game) {
        if (isWaellet()) {
          buyGame(game)
        } else {
          alert('You need Waellet to use this feature!')
        }
      }
    },
    mounted () {
    },
    created: function () {
      // Get game list
      console.log('Retrieving game list...')
      getGameList().then((list) => {
        console.log('Done!')
        this.games = list.decodedResult

        // Get games data
        for (var i in list.decodedResult) {
          console.log(list.decodedResult[i][1])
          let j = i
          var gameAddress = list.decodedResult[j][1]
          this.games[j].address = gameAddress
          call(gameContract, 'get_name', [], gameAddress).then((gameName) => {
            Vue.set(this.games, j, {...this.games[j], ...{ name: gameName.decodedResult }})
            console.log(gameName.decodedResult)
          })
          call(gameContract, 'get_image', [], gameAddress).then((gameImg) => {
            Vue.set(this.games, j, {...this.games[j], ...{ img: gameImg.decodedResult }})
            console.log(gameImg.decodedResult)
          })
          call(gameContract, 'get_url', [], gameAddress).then((gameUrl) => {
            Vue.set(this.games, j, {...this.games[j], ...{ url: gameUrl.decodedResult }})
            console.log(gameUrl.decodedResult)
          })
          call(gameContract, 'get_price', [], gameAddress).then((gamePrice) => {
            Vue.set(this.games, j, {...this.games[j], ...{ price: gamePrice.decodedResult }})
            console.log(gamePrice.decodedResult)
          })
          call(gameContract, 'balance_of', [getUserAddress()], gameAddress).then((balance) => {
            Vue.set(this.games, j, {...this.games[j], ...{ balance: balance.decodedResult }})
            console.log(balance.decodedResult)
          })
        }
      })
      if (typeof window.Aepp !== 'undefined') {
        window.Aepp.request.connect().then(result => {
          console.log('Waellet connected!')
          setWaellet()
        })
      }
    }
  }
</script>
