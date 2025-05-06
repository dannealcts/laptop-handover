const mix = require('laravel-mix');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

// Compile JavaScript and Sass/SCSS
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .sourceMaps();  // Optional: this helps in debugging with sourcemaps

// Enable BrowserSync for hot reloading
mix.webpackConfig({
   plugins: [
      new BrowserSyncPlugin({
         // Automatically open the browser
         open: true,
         // Proxy your Laravel development server
         proxy: 'http://localhost:8000',
         // Watch files for changes
         files: [
            'resources/views/**/*.php',
            'resources/js/**/*.js',
            'resources/sass/**/*.scss'
         ]
      })
   ]
});
