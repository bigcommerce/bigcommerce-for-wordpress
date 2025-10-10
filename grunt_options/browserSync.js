/**
 *
 * Module: grunt-browser-sync
 * Documentation: https://npmjs.org/package/grunt-contrib-clean
 *
 */

const commonFiles = [
  '<%= pkg._bc_css_dist_path %>master.css',
  '<%= pkg._bc_css_dist_path %>master-amp.css',
  '<%= pkg._bc_css_dist_path %>login.css',
  '<%= pkg._bc_css_dist_path %>editor-style.css',
  '<%= pkg._bc_assets_path %>/**/*.php',
  '<%= pkg._bc_assets_path %>/**/*.twig',
  '<%= pkg._bc_public_js_dist_path %>*.js',
  '<%= pkg._bc_admin_js_dist_path %>*.js',
  '<%= pkg._bc_public_img_path %>**/*.jpg',
  '<%= pkg._bc_admin_img_path %>**/*.jpg',
  '<%= pkg._bc_plugin_path %>assets/**/*.css',
  '<%= pkg._bc_plugin_path %>assets/**/*.js',
  '<%= pkg._bc_plugin_path %>**/*.php',
];

const commonOptions = {
  watchTask: true,
  debugInfo: true,
  logConnections: true,
  notify: true,
  ghostMode: {
    scroll: true,
    links: true,
    forms: true,
  },
};

module.exports = {
  dev: {
    bsFiles: { src: commonFiles },
    options: {
      ...commonOptions,
      proxy: '<%= dev.proxy %>',
    },
  },
  devDocker: {
    bsFiles: { src: commonFiles },
    options: {
      ...commonOptions,
      open: 'external',
      host: '<%= dev.proxy %>',
      proxy: 'https://<%= dev.proxy %>',
      https: {
        key: '<%= dev.certs_path %>/<%= dev.proxy %>.key',
        cert: '<%= dev.certs_path %>/<%= dev.proxy %>.crt',
      },
    },
  },
};
