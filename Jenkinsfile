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
                    },
                    'stan': {
                        sh 'make stan'
                    },
                    'test': {
                        sh 'make test'
                    }
                )
            }
            post {
                failure {
                    withCredentials([string(credentialsId: '8ffb1dc7-4858-4c4a-ac9e-0a1d655a3b59', variable: 'GITHUB_TOKEN')]) {
                        step([
                            $class: 'ViolationsToGitHubRecorder',
                            config: [
                                gitHubUrl: 'https://api.github.com/',
                                repositoryOwner: 'wizaplace',
                                repositoryName: 'wizaplace-php-sdk',
                                pullRequestId: '29',
                                useOAuth2Token: true,
                                oAuth2Token: "$GITHUB_TOKEN",
                                useUsernamePassword: false,
                                useUsernamePasswordCredentials: false,
                                usernamePasswordCredentialsId: '',
                                createCommentWithAllSingleFileComments: true,
                                createSingleFileComments: true,
                                commentOnlyChangedContent: true,
                                minSeverity: 'INFO',
                                violationConfigs: [
                                    [ pattern: '.*/coke-checkstyle/.*\\.xml$', reporter: 'CHECKSTYLE' ],
                                ]
                            ]
                        ])
                    }
                }
                always {
                    junit 'coke-result.xml'
                    junit 'phpunit-result.xml'
                    step([
                        $class: 'CloverPublisher',
                        cloverReportDir: './',
                        cloverReportFileName: 'clover.xml'
                    ])
                }
            }
        }
    }
}
