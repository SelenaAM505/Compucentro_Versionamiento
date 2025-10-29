pipeline {
    agent any

    environment {
        DOCKER_COMPOSE_FILE     = "docker-compose.yml"
        PROJECT_DIR             = "www/CompuCentro_Coban"
        REPORT_DIR              = "reports"
        REPORT_FILE             = "Reporte_SonarQube_CompuCentro.pdf"
        SONARQUBE_SERVER        = "SonarQubeServer"
        SONARQUBE_PROJECT_KEY   = "compucentro"
        SONARQUBE_TOKEN         = credentials('sonarqube-token')
    }

    stages {
        stage('Clonar Repositorio') {
            steps {
                echo "📦 Clonando el repositorio de GitHub..."
                git branch: 'main',
                    credentialsId: 'github-token',
                    url: 'https://github.com/SelenaAM505/Compucentro_Versionamiento.git'
            }
        }

        stage('Análisis con SonarQube') {
            steps {
                echo "🔍 Iniciando análisis de código con SonarQube..."
                withSonarQubeEnv(SONARQUBE_SERVER) {
                    sh '''
                        sonar-scanner \
                            -Dsonar.projectKey=compucentro \
                            -Dsonar.sources=www/CompuCentro_Coban \
                            -Dsonar.host.url=http://sonarqube:9000 \
                            -Dsonar.token=$SONARQUBE_TOKEN
                    '''
                }
            }
        }

        stage('Esperar Resultado de Análisis') {
            steps {
                script {
                    echo "⏳ Esperando resultados de calidad de SonarQube..."
                    timeout(time: 15, unit: 'MINUTES') {
                        waitForQualityGate abortPipeline: false
                    }
                }
            }
        }

        stage('Construir y Desplegar con Docker') {
            steps {
                echo "🐳 Construyendo e iniciando contenedores Docker..."
                sh '''
                    docker compose down || true
                    docker compose up -d --build
                '''
            }
        }

        stage('Verificar Despliegue') {
            steps {
                echo "✅ Verificando que el sitio esté en línea..."
                sh '''
                    if curl -f http://localhost:8081 > /dev/null 2>&1; then
                        echo '🌐 Sitio operativo correctamente.'
                    else
                        echo '❌ Error al verificar el despliegue'
                    fi
                '''
            }
        }
    }

    post {
        always {
            script {
                def estado = currentBuild.currentResult

                echo "🧾 Generando reporte final..."

                sh '''
                    mkdir -p reports

                    echo "# 📋 Reporte de Ejecución Jenkins" > reports/reporte.md
                    echo "**Proyecto:** CompuCentro Cobán WebApp" >> reports/reporte.md
                    echo "**Fecha:** $(date)" >> reports/reporte.md
                    echo "**Estado del Pipeline:** '''${estado}'''" >> reports/reporte.md
                    echo "\n---\n## Resultados de SonarQube" >> reports/reporte.md

                    curl -s -u $SONARQUBE_TOKEN: \
                        "http://sonarqube:9000/api/measures/component?component=compucentro&metricKeys=bugs,vulnerabilities,code_smells,duplicated_lines_density,coverage" \
                        | jq -r '.component.measures[] | "- **\(.metric):** \(.value)"' >> reports/reporte.md

                '''

                archiveArtifacts artifacts: "reports/*.md", fingerprint: true
            }
        }
    }
}
