pipeline {
    agent any

    environment {
        SONARQUBE_SERVER = 'SonarQube'  // Nombre configurado en Jenkins > Manage Jenkins > SonarQube servers
        REPORT_PATH = 'cicd/reportes'
        EMAIL = 'sele015vespino@gmail.com'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/SelenaAM505/Compucentro_Versionamiento.git'
            }
        }

        stage('Análisis SonarQube') {
            steps {
                withSonarQubeEnv("${SONARQUBE_SERVER}") {
                    sh '''
                        sonar-scanner \
                        -Dsonar.projectKey=CompuCentro \
                        -Dsonar.sources=./www \
                        -Dsonar.host.url=http://sonarqube:9000 \
                        -Dsonar.login=admin \
                        -Dsonar.password=admin
                    '''
                }
            }
        }

        stage('Esperar Resultados') {
            steps {
                timeout(time: 2, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Generar PDF') {
            steps {
                sh '''
                    echo "📄 Generando reporte PDF..."
                    cd cicd/reportes
                    curl -u admin:admin "http://sonarqube:9000/api/issues/search?componentKeys=CompuCentro" \
                        | jq '.' > reporte.html
                    pandoc reporte.html -o reporte.pdf --pdf-engine=wkhtmltopdf
                '''
            }
        }

        stage('Enviar Correo') {
            steps {
                sh '''
                    echo "📧 Enviando correo con el reporte..."
                    echo "Adjunto el reporte de análisis de SonarQube." \
                    | mail -s "Reporte CI/CD - CompuCentro" -A cicd/reportes/reporte.pdf ${EMAIL}
                '''
            }
        }
    }

    post {
        always {
            echo '🔔 Pipeline finalizado.'
        }
    }
}