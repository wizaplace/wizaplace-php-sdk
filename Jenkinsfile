pipeline {
    agent {
        dockerfile {
            args '-v /composer'
        }
    }

    stages {
        stage('setup') {
            steps {
                sh 'make install'
            }
        }
        stage('check') {
            steps {
                parallel(
                    'lint': {
                        sh 'make lint'
                    },
                    'test': {
                        sh 'make test'
                    }
                )
            }
        }
    }
}
