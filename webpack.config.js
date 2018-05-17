var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');


module.exports = {
  entry: [
    'tether',
    './browser/Main.js',
  ],
  output: {
    path: path.resolve(__dirname, 'www'),
    filename: 'app.js'
  },
  module: {
    loaders: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: "babel-loader"
      },
      {
        test: /\.scss$/,
        loader: ExtractTextPlugin.extract(['css-loader', 'sass-loader'])
      },
    ]
  },
  stats: {
    colors: true
  },
  devtool: 'source-map',
  plugins: [
    new webpack.ProvidePlugin({
      $: "jquery",
      jQuery: "jquery",
      "window.jQuery": "jquery",
      Tether: "tether",
      "window.Tether": "tether",
      //Alert: "exports-loader?Alert!bootstrap/js/dist/alert",
      Button: "exports-loader?Button!bootstrap/js/dist/button",
      //Carousel: "exports-loader?Carousel!bootstrap/js/dist/carousel",
      //Collapse: "exports-loader?Collapse!bootstrap/js/dist/collapse",
      Dropdown: "exports-loader?Dropdown!bootstrap/js/dist/dropdown",
      //Modal: "exports-loader?Modal!bootstrap/js/dist/modal",
      //Popover: "exports-loader?Popover!bootstrap/js/dist/popover",
      //Scrollspy: "exports-loader?Scrollspy!bootstrap/js/dist/scrollspy",
      //Tab: "exports-loader?Tab!bootstrap/js/dist/tab",
      Tooltip: "exports-loader?Tooltip!bootstrap/js/dist/tooltip",
      Util: "exports-loader?Util!bootstrap/js/dist/util",
    }),
    new ExtractTextPlugin({
      filename: 'app.css',
      disable: false,
      allChunks: true,
    }),
    new webpack.LoaderOptionsPlugin({
      minimize: true,
      debug: false,
    }),
    new webpack.ContextReplacementPlugin(/moment[\/\\]locale$/, /cs/),
    new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
    new webpack.optimize.UglifyJsPlugin({
      minimize: true,
      sourceMap: true,
      compress: {
        drop_console: false
      },
      mangle: {
        except: ['$super', '$', 'exports', 'require', '$q', '$ocLazyLoad']
      }
    })
  ]
};