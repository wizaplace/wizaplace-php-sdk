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
                    archiveArtifacts allowEmptyArchive: true, artifacts: 'phpstan-checkstyle.xml'
                    archiveArtifacts allowEmptyArchive: true, artifacts: 'phpcs-checkstyle.xml'
                    withCredentials([string(credentialsId: 'e18082c0-a95c-4c22-9bf5-803fd091c764', variable: 'GITHUB_TOKEN')]) {
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
                                createCommentWithAllSingleFileComments: false,
                                createSingleFileComments: true,
                                commentOnlyChangedContent: true,
                                minSeverity: 'INFO',
                                violationConfigs: [
                                    [ pattern: '.*/phpstan-checkstyle\\.xml$', parser: 'CHECKSTYLE', reporter: 'phpstan' ],
                                    [ pattern: '.*/phpcs-checkstyle\\.xml$', parser: 'CHECKSTYLE', reporter: 'phpcs' ],
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
                    publishHTML (target: [
                      allowMissing: false,
                      alwaysLinkToLastBuild: false,
                      keepAll: true,
                      reportDir: 'coverage',
                      reportFiles: 'index.html',
                      reportName: "Code Coverage report"
                    ])
                }
            }
        }
    }
}
