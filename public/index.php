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

  $country = new \models\Country();
  $lang = new \models\Lang();

  $sesHandler = new MySQLSessionHandler(new mysqli('localhost', \DB::$user, \DB::$password, \DB::$dbName));
  $sesHandler->start();

  $country_id = $country->restoreParam();
  $lang_id = $lang->restoreParam();
  
?>
    
    <div class="container" id="tags">
      <div class="row">
        <div class="col-auto">
          <button type="button" class="btn btn-primary me-2" v-for="tag in etags">
            {{tag}}<span class="badge bg-secondary ms-2" @click="onEditableTagClick" v-bind="{'data-name': tag}">X</span>
          </button>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-lg-8 col-md-7 col-sm-5">
          <input 
            type="text" 
            class="form-control mb-10" 
            placeholder="Tags, type 'tag1,tag2, ...' to search tags used with tags" 
            aria-label="Search tags" 
            aria-describedby="basic-addon1"
            @keyup="onInputChange"
            v-model="searchInput"
          >
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3">
          <select class="form-select" aria-label="Language" @change="onLangChange">
            <?php foreach ($lang->get() as $record): ?>
              <option value='<?=$record['id']?>' <?php if ($lang_id === (int) $record['id']):?>selected='selected'<?php endif; ?>><?=$record['name']?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4">
          <select class="form-select" aria-label="Country" @change="onCountryChange">
            <?php foreach ($country->get() as $record): ?>
              <option value='<?=$record['id']?>' <?php if ($country_id === (int) $record['id']):?>selected='selected'<?php endif; ?>><?= $record['name'] === '' ? 'Worldwide' : $record['name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-auto">
          <button type="button" class="btn btn-primary me-2" v-for="tag in tags"  @click="onTagClick" v-bind="{'data-name': tag.name}">
            {{tag.name}}
            <span class="badge bg-secondary">{{tag.products_count}}</span>
          </button>
          <button type="button" class="btn btn-link me-2" v-if="searchInput!=''">
            <a href='/p.php'>Go To Products &gt; &gt; &gt;</a>
            <span class="badge bg-secondary ms-2">{{products_count}}</span>
          </button>
        </div>
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
            tags: [],
            etags: [],
            searchInputOld: "",
            searchInput: "",
            lang: 1,
            country: 1,
            products_count: 0,
            selTags: {},

            _timeout: false,
            _timeoutSeconds: 500
          }
        },
        methods: {
          onLangChange: function (event) {
            console.log('onLangChange', event.target.value)
            this.lang = event.target.value;
            this.showTags();
          },
          onCountryChange: function (event) {
            console.log('onCountryChange', event.target.value)
            this.country = event.target.value;
            this.showTags();
          },
          onInputChange: function (event) {
            clearTimeout(this._timeout);
            // this.searchInput = event.target.value

            this._timeout = setTimeout(() => {
              if (this.searchInputOld != event.target.value) {
                this.searchInputOld = event.target.value;
                this.showTags();
              }
            }, this._timeoutSeconds);

            this.showEditableTags()
          },
          onTagClick: function (event) {
            var tagName = event.target.dataset.name
            var tags = this.searchInput.split(',')
            var newTags = []

            if (tags.indexOf(tagName) == -1) {
              tags.pop()

              tags.push(tagName)
              tags.push('')
              this.searchInput = tags.join(',')
              this.showTags();
              this.showEditableTags();
            }
          },
          onEditableTagClick: function (event) {
            var tagName = event.target.dataset.name
            var tags = this.searchInput.split(',')
            var newTags = []

            for (var i in tags) {
              if (tags[i] != tagName) {
                newTags.push(tags[i]);
              }
            }

            this.searchInput = newTags.join(',')
            this.showTags();
            this.showEditableTags();
          },
          showEditableTags: function() {
            delete this.etags
            this.etags = this.searchInput.split(',')
            this.etags.pop();
          },
          showTags: function() {
            var self = this
            var tags = this.searchInput.split(',')
            var search = tags.pop();

            axios.get('/tags.php?tags='+tags.join(',')+'&name='+search+'&lang='+this.lang+'&country='+this.country).then(function (results) {
              self.tags = results.data.tags;
              self.products_count = results.data.products_count;
            })
          },
        },
        created() {
          this.showTags();
        }
      }).mount('#tags')
    </script>
  </body>
</html>

<?php
session_write_close();