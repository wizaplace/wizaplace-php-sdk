pipeline {
    agent {
        dockerfile {
            args '-v composer_wizaplace-php-sdk_cache:/composer'
        }
    }

    stages {
        stage('setup') {
            steps {
                sh 'env'
                sh 'make install'
            }
        }
        stage('check') {
            steps {
                parallel(
                    'lint': {
                        sh 'make lint'
                        junit 'coke-result.xml'
                    },
                    'stan': {
                        sh 'make stan'
                    },
                    'test': {
                        sh 'make test'
                        junit 'phpunit-result.xml'
                        step([
                            $class: 'CloverPublisher',
                            cloverReportDir: './',
                            cloverReportFileName: 'clover.xml'
                        ])
                    }
                )
            }
        }
    }
}
