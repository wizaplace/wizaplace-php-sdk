pipeline {
    agent {
        dockerfile {
            args '-v /composer'
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
                    'test': {
                        sh 'make test'
                        junit 'phpunit-result.xml'
                    }
                )
            }
        }
    }
}
