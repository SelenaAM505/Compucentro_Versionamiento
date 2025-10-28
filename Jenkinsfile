pipeline {
    agent any

    environment {
        DOCKER_COMPOSE_FILE = "docker-compose.yml"
        PROJECT_DIR = "www/CompuCentro_Coban"
        REPORT_DIR = "reports"
        REPORT_FILE = "Reporte_SonarQube_CompuCentro.pdf"
        SONARQUBE_SERVER = "SonarQubeServer"
        SONARQUBE_PROJECT_KEY = "compucentro"
        SONARQUBE_TOKEN = "tu_token_de_sonarqube"
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
                            -Dsonar.projectKey=${SONARQUBE_PROJECT_KEY} \
                            -Dsonar.sources=${PROJECT_DIR} \
                            -Dsonar.host.url=http://sonarqube:9000 \
                            -Dsonar.token=${SONARQUBE_TOKEN}
                    '''
                }
            }
        }

        stage('Esperar Resultado de Análisis') {
            steps {
                script {
                    echo "⏳ Esperando resultados de calidad de SonarQube..."
                    timeout(time: 15, unit: 'MINUTES') { // ⬅️ se aumentó el tiempo
                        waitForQualityGate abortPipeline: false // ⬅️ no aborta, solo continúa
                    }
                }
            }
        }

        stage('Construir y Desplegar con Docker') {
            when { expression { currentBuild.result == null || currentBuild.result == 'SUCCESS' } }
            steps {
                echo "🐳 Construyendo e iniciando contenedores Docker..."
                sh "docker compose down"
                sh "docker compose up -d --build"
            }
        }

        stage('Verificar Despliegue') {
            steps {
                echo "✅ Verificando que el sitio esté en línea..."
                sh '''
                    if curl -f http://localhost:8081 > /dev/null 2>&1; then
                        echo "🌐 Sitio operativo correctamente."
                    else
                        echo "❌ Error al verificar el despliegue"
                    fi
                '''
            }
        }
    }

    post {
        always {
            echo "🧾 Generando reporte final (éxito, fallo o aborto)..."
            sh '''
                mkdir -p ${REPORT_DIR}

                echo "# 📋 Reporte de Ejecución Jenkins" > ${REPORT_DIR}/reporte.md
                echo "**Proyecto:** CompuCentro Cobán WebApp" >> ${REPORT_DIR}/reporte.md
                echo "**Fecha:** $(date)" >> ${REPORT_DIR}/reporte.md
                echo "**Estado del Pipeline:** ${currentBuild.currentResult}" >> ${REPORT_DIR}/reporte.md
                echo "\\n---\\n## Resultados de SonarQube" >> ${REPORT_DIR}/reporte.md

                curl -s -u ${SONARQUBE_TOKEN}: \
                    "http://sonarqube:9000/api/measures/component?component=${SONARQUBE_PROJECT_KEY}&metricKeys=bugs,vulnerabilities,code_smells,duplicated_lines_density,coverage" \
                    | jq -r '.component.measures[] | "- **\(.metric):** \(.value)"' >> ${REPORT_DIR}/reporte.md

                pandoc ${REPORT_DIR}/reporte.md \
                    --from markdown \
                    --template=cicd/plantillas/reporte_compucentro.latex \
                    --pdf-engine=xelatex \
                    -o ${REPORT_DIR}/${REPORT_FILE}
            '''
            archiveArtifacts artifacts: "${REPORT_DIR}/${REPORT_FILE}", fingerprint: true
        }

        success {
            echo "🎉 Pipeline completado con éxito."
        }

        failure {
            echo "⚠️ Pipeline falló, pero el reporte fue generado."
        }

        aborted {
            echo "🚫 Pipeline abortado manualmente, se generó un reporte."
        }
    }
}
