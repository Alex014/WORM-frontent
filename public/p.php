<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

    <title>WORM - World Object Markup Language</title>
  </head>
  <body>
    <center>
      <h1>Hello, WORM!</h1>
    </center>

<?php 
  ini_set('display_errors', 'yes');
  error_reporting(E_ALL);

  require '../lib/MySQLSessionHandler.php';
  require '../models/country.php';
  require '../models/lang.php';
  require '../models/tags.php';

  $country = new \models\Country();
  $lang = new \models\Lang();
  $tags = new \models\Tags();

  $sesHandler = new MySQLSessionHandler(new mysqli('localhost', \DB::$user, \DB::$password, \DB::$dbName));
  $sesHandler->start();

  $country_id = $country->restoreParam();
  $lang_id = $lang->restoreParam();

  $tagsId = $tags->restoreTags();
  $tagsList = $tags->getNamesFromId($tagsId);
?>
  <div class="container" id="products">
      <div class="row mb-2">
        <div class="col-12 text-center">
          <a href='/' class="card-link">GO TO TAGS</a>
        </div> 
      </div>

      <div class="row mt-4">
        <div class="col-auto">
            <?php foreach ($tagsList as $tag): ?>
          <div class="form-check form-check-inline">
            <input class="form-check-input" v-model="selTags" @change="onTagClick" type="checkbox" id="tag<?=$tag['id']?>" name="tag<?=$tag['id']?>" value="<?=$tag['id']?>" checked="checked">
            <label class="form-check-label" for="tag<?=$tag['id']?>"><?=$tag['name']?> <span class="badge bg-secondary"><?=$tag['products_count']?></span></label>
          </div>
            <?php endforeach;?>
        </div>
      </div>

      <div class="row mt-4">
        <div class="input-group mb-12">
          <span class="input-group-text">Price from: </span>
          <input type="text" class="form-control" v-model="priceFrom" @change="onPriceChange">
          <span class="input-group-text"> to: </span>
          <input type="text" class="form-control" v-model="priceTo" @change="onPriceChange">
        </div>
      </div>

      <div class="row mt-4">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">IMG</th>
              <th scope="col" nowrap="nowrap">Name and URL</th>
              <th scope="col">Tags</th>
              <th scope="col">Price</th>
              <th scope="col">Descr</th>
              <th scope="col" colspan=2>Marketplace</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products">
              <td>
              <img v-bind="{src: product.img}" width=24 height=24>
              </td>
              <td nowrap="nowrap">
                <a v-bind="{href: product.url}">{{product.name}}</a>
              </td>
              <td nowrap="nowrap">{{product.tags}}</td>
              <td>{{product.price}}</td>
              <td>{{product.descr}}</td>
              <td>
              <img v-if='product.marketplace_img' v-bind="{src: product.marketplace_img}" width=24 height=24>
              </td>
              <td nowrap="nowrap">
                <a v-bind="{href: product.marketplace_url}">{{product.marketplace_name}}</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
    -->
    <script src="https://unpkg.com/vue@next"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script type="text/javascript">
    Vue.createApp({
        data() {
          return {
            products: [],
            selTags: <?=json_encode($tagsId)?>,
            priceFrom: 0.0,
            priceTo: 0.0,

            _timeout: false,
            _timeoutSeconds: 500
          }
        },
        methods: {
          onTagClick: function (event) {
            console.log('onTagClick', event.target.value, this.selTags)
            var tagName = event.target.dataset.name
            this.priceFrom = 0;
            this.priceTo = 0;
            this.showProducts()
          },
          onSortClick: function (event) {
            console.log('onSortClick', event.target.value)
          },
          onPriceChange: function (event) {
            clearTimeout(this._timeout);

            this._timeout = setTimeout(() => {
                this.showProducts();
            }, this._timeoutSeconds);
          },
          showProducts: function() {
            var self = this

            axios.get('/products.php?tags='+this.selTags.join(',')+'&price_from='+this.priceFrom+'&price_to='+this.priceTo).then(function (results) {
                self.products = results.data;
                var minPrice = 0;
                var maxPrice = 0;

                for (var i in self.products) {
                    var product = self.products[i];

                    if (product.url == null || product.url == '')
                      product.url = '#'

                    if (product.img == null || product.img == '')
                      product.img = '/img/product.png'

                    if (product.marketplace_name == null || product.marketplace_name == '')
                      product.marketplace_name = ' --- '

                    if (product.marketplace_img == null || product.marketplace_img == '')
                      product.marketplace_img = '/img/marketplace.png'

                    if (product.marketplace_url == null || product.marketplace_url == '')
                      product.marketplace_url = '#'

                    if (product.price > maxPrice) maxPrice = product.price;
                    if (minPrice == 0) minPrice = product.price;
                    else if (product.price < minPrice) minPrice = product.price;
                }

                self.priceFrom = minPrice;
                self.priceTo = maxPrice;
            })
          },
        },
        created() {
          this.showProducts();
        }
      }).mount('#products')
    </script>
  </body>
</html>

<?php
session_write_close();