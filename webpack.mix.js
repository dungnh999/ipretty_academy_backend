const { EnvironmentPlugin } = require('webpack');
const mix = require('laravel-mix');
const glob = require('glob');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Configure mix
 |--------------------------------------------------------------------------
 */

mix.options({
  resourceRoot: process.env.ASSET_URL || undefined,
  processCssUrls: false,
  postCss: [require('autoprefixer')]
});

/*
 |--------------------------------------------------------------------------
 | Configure Webpack
 |--------------------------------------------------------------------------
 */

mix.webpackConfig({
  output: {
    publicPath: process.env.ASSET_URL || undefined,
    libraryTarget: 'umd'
  },

  plugins: [
    new EnvironmentPlugin({
      // Application's public url
      BASE_URL: process.env.ASSET_URL ? `${process.env.ASSET_URL}/` : '/'
    })
  ],
  module: {
    rules: [
      {
        test: /\.es6$|\.js$/,
        include: [
          path.join(__dirname, 'node_modules/bootstrap/'),
          path.join(__dirname, 'node_modules/popper.js/'),
          path.join(__dirname, 'node_modules/shepherd.js/'),
          path.join(__dirname, 'node_modules/datatables.net-bs5/')
        ],
        loader: 'babel-loader',
        options: {
          presets: [['@babel/preset-env', { targets: 'last 2 versions, ie >= 10' }]],
          plugins: [
            '@babel/plugin-transform-destructuring',
            '@babel/plugin-proposal-object-rest-spread',
            '@babel/plugin-transform-template-literals'
          ],
          babelrc: false
        }
      }
    ]
  },
  externals: {
    jquery: 'jQuery',
    moment: 'moment',
    jsdom: 'jsdom',
    velocity: 'Velocity',
    hammer: 'Hammer',
    pace: '"pace-progress"',
    chartist: 'Chartist',
    'popper.js': 'Popper',

    // blueimp-gallery plugin
    './blueimp-helper': 'jQuery',
    './blueimp-gallery': 'blueimpGallery',
    './blueimp-gallery-video': 'blueimpGallery'
  }
});

/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

function mixAssetsDir(query, cb) {
  (glob.sync('resources/assets/' + query) || []).forEach(f => {
    f = f.replace(/[\\\/]+/g, '/');
    cb(f, f.replace('resources/assets/', 'public/assets/'));
  });
}

/*
 |--------------------------------------------------------------------------
 | Configure sass
 |--------------------------------------------------------------------------
 */

const sassOptions = {
  precision: 5
};

// Core stylesheets
mixAssetsDir('vendor/scss/**/!(_)*.scss', (src, dest) =>
  mix.sass(src, dest.replace(/(\\|\/)scss(\\|\/)/, '$1css$2').replace(/\.scss$/, '.css'), { sassOptions })
);

// Core javascripts
mixAssetsDir('vendor/js/**/*.js', (src, dest) => mix.js(src, dest));

// Libs
mixAssetsDir('vendor/libs/**/*.js', (src, dest) => mix.js(src, dest));
mixAssetsDir('vendor/libs/**/!(_)*.scss', (src, dest) =>
  mix.sass(src, dest.replace(/\.scss$/, '.css'), { sassOptions })
);
mixAssetsDir('vendor/libs/**/*.{png,jpg,jpeg,gif}', (src, dest) => mix.copy(src, dest));

// Fonts
mixAssetsDir('vendor/fonts/*/*', (src, dest) => mix.copy(src, dest));
mixAssetsDir('vendor/fonts/!(_)*.scss', (src, dest) =>
  mix.sass(src, dest.replace(/(\\|\/)scss(\\|\/)/, '$1css$2').replace(/\.scss$/, '.css'), { sassOptions })
);

/*
 |--------------------------------------------------------------------------
 | Application assets
 |--------------------------------------------------------------------------
 */

mixAssetsDir('js/**/*.js', (src, dest) => mix.scripts(src, dest));
mixAssetsDir('css/**/*.css', (src, dest) => mix.copy(src, dest));

mix.copy('node_modules/boxicons/fonts/*', 'public/assets/vendor/fonts/boxicons');
mix.copy(
  'node_modules/datatables.net-bs5/js/dataTables.bootstrap5.js',
  'public/assets/vendor/libs/dataTable/dataTables.bootstrap5.js'
);

mix.copy(
  'node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.js',
  'public/assets/vendor/libs/dataTable/buttons/js/buttons.bootstrap5.js'
);
mix.copy(
  'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.css',
  'public/assets/vendor/libs/dataTable/buttons/css/buttons.bootstrap5.css'
);
mix.copy(
  'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.css',
  'public/assets/vendor/libs/dataTable/responsive/css/responsive.bootstrap5.css'
);

mix.copy(
  'node_modules/datatables.net-buttons/js/dataTables.buttons.js',
  'public/assets/vendor/libs/dataTable/buttons/js/dataTables.buttons.js'
);
mix.copy(
  'node_modules/datatables.net-responsive/js/dataTables.responsive.js',
  'public/assets/vendor/libs/dataTable/responsive/js/dataTables.responsive.js'
);

// Select2
mix.copy('node_modules/select2/dist/js/select2.js', 'public/assets/vendor/libs/select2/js/select2.js');
mix.copy('node_modules/select2/dist/css/select2.css', 'public/assets/vendor/libs/select2/css/select2.css');

// Sweetalert2
mix.copy(
  'node_modules/sweetalert2/dist/sweetalert2.all.min.js',
  'public/assets/vendor/libs/sweetalert2/js/sweetalert2.js'
);
mix.copy(
  'node_modules/sweetalert2/dist/sweetalert2.min.css',
  'public/assets/vendor/libs/sweetalert2/css/sweetalert2.css'
);

// Cropper
mix.copy('node_modules/cropperjs/dist/cropper.min.js', 'public/assets/vendor/libs/cropper/js/cropper.js');
mix.copy('node_modules/cropperjs/dist/cropper.min.css', 'public/assets/vendor/libs/cropper/css/cropper.css');

// Nouislider
mix.copy('node_modules/nouislider/dist/nouislider.js', 'public/assets/vendor/libs/nouislider/js/nouislider.js');
mix.copy('node_modules/nouislider/dist/nouislider.css', 'public/assets/vendor/libs/nouislider/css/nouislider.css');

// bs stepper
mix.copy('node_modules/bs-stepper/dist/js/bs-stepper.js', 'public/assets/vendor/libs/bs-stepper/js/bs-stepper.js');
mix.copy('node_modules/bs-stepper/dist/css/bs-stepper.css', 'public/assets/vendor/libs/bs-stepper/css/bs-stepper.css');

// bs stepper
mix.copy('node_modules/select2/dist/js/select2.js', 'public/assets/vendor/libs/select2/js/select2.js');
mix.copy('node_modules/select2/dist/css/select2.css', 'public/assets/vendor/libs/select2/css/select2.css');

// plyr video
mix.copy('node_modules/plyr/dist/plyr.js', 'public/assets/vendor/libs/plyr/js/plyr.js');
mix.copy('node_modules/plyr/dist/plyr.css', 'public/assets/vendor/libs/plyr/css/plyr.css');

// Quill
mix.copy('node_modules/quill/dist/quill.js', 'public/assets/vendor/libs/quill/js/quill.js');
mix.copy('node_modules/quill/dist/quill.core.css', 'public/assets/vendor/libs/quill/css/quill.core.css');
mix.copy('node_modules/quill/dist/quill.bubble.css', 'public/assets/vendor/libs/quill/css/quill.bubble.css');
mix.copy('node_modules/quill/dist/quill.snow.css', 'public/assets/vendor/libs/quill/css/quill.snow.css');

// sortablejs
mix.copy('node_modules/sortablejs/Sortable.js', 'public/assets/vendor/libs/sortablejs/Sortable.js');

// jkanban
mix.copy('node_modules/jkanban/dist/jkanban.js', 'public/assets/vendor/libs/jkanban/js/jkanban.js');
mix.copy('node_modules/jkanban/dist/jkanban.css', 'public/assets/vendor/libs/jkanban/css/jkanban.css');

mix.copy(
  'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.css',
  'public/assets/vendor/libs/dataTable/css/dataTables.bootstrap5.css'
);
mix.copy('node_modules/datatables.net/js/jquery.dataTables.js', 'public/assets/vendor/libs/dataTable/dataTables.js');
mix.copy('node_modules/jquery/dist/jquery.js', 'public/assets/vendor/libs/jquery/jquery.js');
mix.copy('node_modules/axios/dist/axios.js', 'public/assets/vendor/libs/axios/axios.js');

mix.version();

/*
 |--------------------------------------------------------------------------
 | Browsersync Reloading
 |--------------------------------------------------------------------------
 |
 | BrowserSync can automatically monitor your files for changes, and inject your changes into the browser without requiring a manual refresh.
 | You may enable support for this by calling the mix.browserSync() method:
 | Make Sure to run `php artisan serve` and `yarn watch` command to run Browser Sync functionality
 | Refer official documentation for more information: https://laravel.com/docs/10.x/mix#browsersync-reloading
 */

mix.browserSync('http://127.0.0.1:8000/');
