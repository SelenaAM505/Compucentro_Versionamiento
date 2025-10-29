pipeline {
    agent any

    environment {
        SONARQUBE_SERVER = 'SonarQube'       // Nombre configurado en Jenkins > Manage Jenkins > SonarQube servers
        SONAR_TOKEN = credentials('sonar-token') // Token guardado en Jenkins (tipo Secret text)
        REPORT_PATH = 'cicd/reportes'
        EMAIL = 'sele015vespino@gmail.com'
    }

    stages {
        stage('Checkout') {
            steps {
                echo "📦 Descargando código desde GitHub..."
                git branch: 'main', url: 'https://github.com/SelenaAM505/Compucentro_Versionamiento.git'
            }
        }

        stage('Análisis SonarQube') {
            steps {
                echo "🔍 Ejecutando análisis de SonarQube..."
                withSonarQubeEnv("${SONARQUBE_SERVER}") {
                    sh '''
                        sonar-scanner \
                        -Dsonar.projectKey=CompuCentro \
                        -Dsonar.sources=./www \
                        -Dsonar.host.url=http://sonarqube:9000 \
                        -Dsonar.token=${SONAR_TOKEN}
                    '''
                }
            }
        }

        stage('Esperar Resultados') {
            steps {
                echo "⏳ Esperando resultados de SonarQube..."
                timeout(time: 2, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Generar PDF') {
            steps {
                echo "📄 Generando reporte PDF..."
                sh '''
                    mkdir -p ${REPORT_PATH}
                    curl -u admin:Holamundo12 "http://sonarqube:9000/api/issues/search?componentKeys=CompuCentro" \
                        | jq '.' > ${REPORT_PATH}/reporte.html
                    pandoc ${REPORT_PATH}/reporte.html -o ${REPORT_PATH}/reporte.pdf --pdf-engine=wkhtmltopdf
                '''
            }
        }

        stage('Enviar Correo') {
            steps {
                echo "📧 Enviando correo con el reporte..."
                sh '''
                    echo "Adjunto el reporte de análisis de SonarQube." \
                    | mail -s "Reporte CI/CD - CompuCentro" -A ${REPORT_PATH}/reporte.pdf ${EMAIL}
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
