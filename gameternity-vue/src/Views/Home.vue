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
                <h1 style="color: #fff; text-shadow: none;" data-animation="animated fadeInDown delay-05s">Decentralized game market</h1>
                <h2 style="color: #fff; text-shadow: none;">Game market place for all</h2>
                
                <!-- SEARCH BAR -->
                <div class="full-width-form" style="margin-top: 165px">
                  <form id="search-bar" style="font-family: montserrat; border-radius: 3px; height: 60px; background: #fff; color: #444; position:relative; overflow: hidden;" >
                    
                    <div class="span_search" style="display: inline-block; width: 80%; height: 55px;vertical-align: top; padding-top: 0px;">
                      <input @focus="showResults=true" @blur="showResults=false" type="text" placeholder="Search by game title, company or keyword" id="camp_search" v-model="search" style="width: 100%; padding-right: 20px; height: 53px; font-size: 14px; vertical-align: middle; padding-left: 10px;">
                    </div>
                    
                    <button type="button" class="btn btn-info" style="background: rgb(255, 13, 106); border-color: #fff;
                      text-transform: uppercase;font-weight: bold;padding: 2px;margin-bottom: 2px; border-radius: 0 3px 3px 0;
                      position:absolute; right: 0; height: 58px; top: 0; padding-right: 20px; padding-left: 20px; font-size: 14px;
                      font-weight: 0;" 
                      v-on:click="searchCampaigns()">
                        Search
                    </button>
                  </form>
                  <!-- SEARCH BAR RESULTS -->
                  <div class="search-results" style="background: #fff; color: #444;" v-if="showResults">
                    <div class="search-entity" v-for="game in games" style="">
                      <img :src="game.img" style="width: 100px;"/> {{ game.name }}
                    </div>
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
            <div class="font-size-16px">No matter if you are a big publisher or a small indie developer, everyone have the same opportunities in Gameternity</div>
            <div class="spacer clear " style="height:50px;"></div>
            <div class="h1-separator medium-blue"></div>
          </div>
        </div>
        <div class="row games-container">
          <div class="col-md-6" v-for="game in games">
            <a :href="game.url" title="game.name" class="text-center game-card width-100">
              <div class="game-name">
                {{ game.name }} <br><small class="text-uppercase"></small>
              </div>
              <img alt="" :src="game.img" class="lazyload">
            </a>
            <div style="width: 150px; height: 50px; position: absolute; left: 20px; bottom: 20px; color: #eee; font-size: 15px; background: rgba(100,100,100,0.5); border-radius: 24px;">
              <img src="/static/img/Aeternity.svg" style="width: 50px"> {{ game.price }} AE
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import Vue from 'vue'
  import { getGameList, gameContract, call } from '@/Common/contract'

  export default {
    name: 'home',
    data: () => ({
      games: [],
      search: '',
      showResults: false
    }),
    computed: {
      style () {
        return 'background-image: url(static/img/bg_home.jpg); background-position-x: -00px;'
      }
    },
    methods: {
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
          call(gameContract, 'get_name', [], list.decodedResult[j][1]).then((gameName) => {
            Vue.set(this.games, j, {...this.games[j], ...{ name: gameName.decodedResult }})
            console.log(gameName.decodedResult)
          })
          call(gameContract, 'get_image', [], list.decodedResult[j][1]).then((gameImg) => {
            Vue.set(this.games, j, {...this.games[j], ...{ img: gameImg.decodedResult }})
            console.log(gameImg.decodedResult)
          })
          call(gameContract, 'get_url', [], list.decodedResult[j][1]).then((gameUrl) => {
            Vue.set(this.games, j, {...this.games[j], ...{ url: gameUrl.decodedResult }})
            console.log(gameUrl.decodedResult)
          })
          call(gameContract, 'get_price', [], list.decodedResult[j][1]).then((gamePrice) => {
            Vue.set(this.games, j, {...this.games[j], ...{ price: gamePrice.decodedResult }})
            console.log(gamePrice.decodedResult)
          })
        }
      })
    }
  }
</script>
