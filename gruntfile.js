module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> (<%= pkg.version %>) - <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                compress: { drop_console: true }
            },
            admin: {
                src: 'admin/js/content-per-user-admin.js',
                dest: 'admin/js/prod/content-per-user-admin.<%= pkg.version %>.min.js'
            },
            admin_options: {
                    src: 'admin/js/content-per-user-options.js',
                    dest: 'admin/js/prod/content-per-user-options.<%= pkg.version %>.min.js'
            },
            profile_admin: {
                src: 'admin/js/content-per-user-profile-admin.js',
                dest: 'admin/js/prod/content-per-user-profile-admin.<%= pkg.version %>.min.js'
            },
            requests_admin: {
                src: 'admin/js/content-per-user-requests-admin.js',
                dest: 'admin/js/prod/content-per-user-requests-admin.<%= pkg.version %>.min.js'
            },
            public: {
                src: 'public/js/content-per-user-public.js',
                dest: 'public/js/prod/content-per-user-public.<%= pkg.version %>.min.js'
            },
        },
        watch: {
            scripts: {
                files: ['admin/js/*.js','public/js/*.js'],
                tasks: ['uglify'],
            },
                php: {
              files: ['*.php', '**/*.php']
            },
            options: {
                livereload: true,
                spawn: false
            }
        },
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default', ['uglify', 'watch']);
};