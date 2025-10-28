pipeline {
    agent any

    environment {
        // ⚙️ Variables globales
        DOCKER_COMPOSE_FILE = "docker-compose.yml"
        PROJECT_DIR = "www/CompuCentro_Coban"
        REPORT_DIR = "reports"
        REPORT_FILE = "Reporte_SonarQube_CompuCentro.pdf"
        SONARQUBE_SERVER = "SonarQubeServer"       // Nombre registrado en Jenkins
        SONARQUBE_PROJECT_KEY = "compucentro"     // ID del proyecto en SonarQube
        SONARQUBE_TOKEN = "tu_token_de_sonarqube" // Token de SonarQube
    }

    stages {

        // 1️⃣ Clonar repositorio
        stage('Clonar Repositorio') {
            steps {
                echo "📦 Clonando el repositorio de GitHub..."
                git branch: 'main',
                    credentialsId: 'github-token',
                    url: 'https://github.com/SelenaAM505/Compucentro_Versionamiento.git'
            }
        }

        // 2️⃣ Análisis con SonarQube
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

        // 3️⃣ Esperar resultado de calidad
        stage('Esperar Resultado de Análisis') {
            steps {
                script {
                    echo "⏳ Esperando resultados de calidad de SonarQube..."
                    timeout(time: 6, unit: 'MINUTES') {
                        waitForQualityGate abortPipeline: true
                    }
                }
            }
        }

        // 4️⃣ Construir y desplegar con Docker
        stage('Construir y Desplegar con Docker') {
            when {
                expression { currentBuild.result == null || currentBuild.result == 'SUCCESS' }
            }
            steps {
                echo "🐳 Construyendo e iniciando contenedores Docker..."
                sh "docker compose down"
                sh "docker compose up -d --build"
            }
        }

        // 5️⃣ Verificar despliegue
        stage('Verificar Despliegue') {
            steps {
                echo "✅ Verificando que el sitio esté en línea..."
                sh '''
                    if curl -f http://localhost:8081 > /dev/null 2>&1; then
                        echo "🌐 Sitio operativo correctamente."
                    else
                        echo "❌ Error al verificar el despliegue"
                        exit 1
                    fi
                '''
            }
        }

        // 6️⃣ Generar reporte PDF profesional con Pandoc
        stage('Generar Reporte PDF') {
            steps {
                echo "🧾 Generando reporte PDF de resultados..."
                sh '''
                    mkdir -p ${REPORT_DIR}

                    # Obtener métricas principales de SonarQube (API REST)
                    curl -s -u ${SONARQUBE_TOKEN}: \
                        "http://sonarqube:9000/api/measures/component?component=${SONARQUBE_PROJECT_KEY}&metricKeys=bugs,vulnerabilities,code_smells,duplicated_lines_density,coverage" \
                        | jq '.' > ${REPORT_DIR}/metricas.json

                    # Crear reporte en formato Markdown
                    echo "# 📊 Reporte de Análisis SonarQube" > ${REPORT_DIR}/reporte.md
                    echo "Proyecto: **CompuCentro Cobán WebApp**" >> ${REPORT_DIR}/reporte.md
                    echo "\\nFecha de generación: $(date)" >> ${REPORT_DIR}/reporte.md
                    echo "\\n---" >> ${REPORT_DIR}/reporte.md
                    echo "\\n## Resultados de Análisis" >> ${REPORT_DIR}/reporte.md
                    jq -r '.component.measures[] | "- **\(.metric):** \(.value)"' ${REPORT_DIR}/metricas.json >> ${REPORT_DIR}/reporte.md

                    echo "\\n## Estado del Pipeline" >> ${REPORT_DIR}/reporte.md
                    echo "✔️ El pipeline se ejecutó correctamente con integración SonarQube + Docker." >> ${REPORT_DIR}/reporte.md

                    # Generar PDF con la plantilla institucional
                    pandoc ${REPORT_DIR}/reporte.md \
                        --from markdown \
                        --template=cicd/plantillas/reporte_compucentro.latex \
                        --pdf-engine=xelatex \
                        -o ${REPORT_DIR}/${REPORT_FILE}
                '''
                archiveArtifacts artifacts: "${REPORT_DIR}/${REPORT_FILE}", fingerprint: true
            }
        }
    }

    post {

        // ✅ Éxito
        success {
            echo "🎉 Despliegue exitoso y reporte generado."
        }

        // ❌ Fallo en alguna etapa
        failure {
            echo "⚠️ Error durante el proceso. Generando reporte de fallo..."
            sh '''
                mkdir -p ${REPORT_DIR}
                echo "# ❌ Pipeline Fallido" > ${REPORT_DIR}/reporte.md
                echo "Fecha: $(date)" >> ${REPORT_DIR}/reporte.md
                echo "\\nEl proceso falló antes de completarse." >> ${REPORT_DIR}/reporte.md
                pandoc ${REPORT_DIR}/reporte.md \
                    --from markdown \
                    --template=cicd/plantillas/reporte_compucentro.latex \
                    --pdf-engine=xelatex \
                    -o ${REPORT_DIR}/${REPORT_FILE}
            '''
            archiveArtifacts artifacts: "${REPORT_DIR}/${REPORT_FILE}", fingerprint: true
        }

        // 🚫 Abortado manualmente
        aborted {
            echo "🚫 Pipeline abortado manualmente. Generando reporte..."
            sh '''
                mkdir -p ${REPORT_DIR}
                echo "# ⚠️ Pipeline Abortado" > ${REPORT_DIR}/reporte.md
                echo "Fecha: $(date)" >> ${REPORT_DIR}/reporte.md
                echo "\\nEl proceso fue interrumpido antes de finalizar." >> ${REPORT_DIR}/reporte.md
                pandoc ${REPORT_DIR}/reporte.md \
                    --from markdown \
                    --template=cicd/plantillas/reporte_compucentro.latex \
                    --pdf-engine=xelatex \
                    -o ${REPORT_DIR}/${REPORT_FILE}
            '''
            archiveArtifacts artifacts: "${REPORT_DIR}/${REPORT_FILE}", fingerprint: true
        }
    }
}
