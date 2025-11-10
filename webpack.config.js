const path = require("path");

module.exports = [
  // Configuration pour l'application principale (graph)
  {
    entry: {
      app: "./assets/js/app.js",
      admin: "./assets/js/admin.js",
    },
    output: {
      path: path.resolve(__dirname, "dist/js"),
      filename: "[name].bundle.js",
    },
    module: {
      rules: [
        {
          test: /\.jsx?$/,
          exclude: /node_modules/,
          use: {
            loader: "babel-loader",
            options: {
              presets: ["@babel/preset-env", "@babel/preset-react"],
            },
          },
        },
        {
          test: /\.scss$/,
          use: ["style-loader", "css-loader", "sass-loader"],
        },
        {
          test: /\.css$/,
          use: ["style-loader", "css-loader"],
        },
      ],
    },
    resolve: {
      extensions: [".js", ".jsx"],
      alias: {
        "@components": path.resolve(__dirname, "assets/js/components"),
        "@utils": path.resolve(__dirname, "assets/js/utils"),
      },
    },
    optimization: {
      splitChunks: {
        cacheGroups: {
          vendors: {
            test: /[\\/]node_modules[\\/](react|react-dom|d3)[\\/]/,
            name: "vendors",
            chunks: "all",
          },
        },
      },
    },
  },
  // Configuration pour les blocks Gutenberg
  {
    entry: {
      "blocks-editor": "./assets/js/blocks-editor.js",
      "article-manager-block": "./assets/js/blocks/article-manager.jsx",
      "image-block": "./assets/js/blocks/image-block.jsx",
      "hero-cover": "./assets/js/blocks/hero-cover.jsx",
      "comparison-slider": "./assets/js/comparison-slider.js",
      "interactive-map": "./assets/js/blocks/interactive-map.jsx",
      "d3-bar-chart": "./assets/js/blocks/d3-bar-chart.jsx",
      "d3-timeline": "./assets/js/blocks/d3-timeline.jsx",
    },
    output: {
      path: path.resolve(__dirname, "dist/js"),
      filename: "[name].bundle.js",
    },
    module: {
      rules: [
        {
          test: /\.jsx?$/,
          exclude: /node_modules/,
          use: {
            loader: "babel-loader",
            options: {
              presets: [
                "@babel/preset-env",
                [
                  "@babel/preset-react",
                  {
                    pragma: "wp.element.createElement",
                    pragmaFrag: "wp.element.Fragment",
                  },
                ],
              ],
            },
          },
        },
      ],
    },
    externals: {
      react: "React",
      "react-dom": "ReactDOM",
      "@wordpress/blocks": ["wp", "blocks"],
      "@wordpress/element": ["wp", "element"],
      "@wordpress/components": ["wp", "components"],
      "@wordpress/block-editor": ["wp", "blockEditor"],
      "@wordpress/editor": ["wp", "editor"],
      "@wordpress/data": ["wp", "data"],
      "@wordpress/i18n": ["wp", "i18n"],
      "@wordpress/server-side-render": ["wp", "serverSideRender"],
    },
    resolve: {
      extensions: [".js", ".jsx"],
    },
  },
];
