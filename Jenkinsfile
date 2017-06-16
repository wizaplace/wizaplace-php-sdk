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
                always {
                    withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                        step([
                            $class: 'ViolationsToGitHubRecorder',
                            config: [
                                gitHubUrl: 'https://api.github.com/',
                                repositoryOwner: 'wizaplace',
                                repositoryName: 'wizaplace-php-sdk',
                                pullRequestId: "${env.CHANGE_ID}",
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
                                    [ pattern: '.*/coke-checkstyle\\.xml$', reporter: 'CHECKSTYLE' ],
                                ]
                            ]
                        ])
                    }
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
