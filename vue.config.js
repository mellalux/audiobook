const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  //publicPath: process.env.NODE_ENV === 'production' ? '/audiobook/' : '/',
  chainWebpack: config => {
    config.plugin('html').tap(args => {
      args[0].title = 'AudioBook Player';
      return args;
    });
  }
})
