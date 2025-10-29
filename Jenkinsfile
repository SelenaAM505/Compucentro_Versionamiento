pipeline {
    agent any

    environment {
        SONARQUBE_SERVER = "SonarQubeServer"
        SONARQUBE_TOKEN  = credentials('sonarqube-token')
    }

    stages {

        stage('Clonar Repositorio') {
            steps {
                echo "📦 Clonando el repositorio..."
                git branch: 'main',
                    credentialsId: 'github-token',
                    url: 'https://github.com/SelenaAM505/Compucentro_Versionamiento.git'
            }
        }

        stage('Análisis con SonarQube') {
            steps {
                echo "🔍 Analizando código..."
                withSonarQubeEnv(SONARQUBE_SERVER) {
                    sh """
                        sonar-scanner \
                            -Dsonar.projectKey=compucentro \
                            -Dsonar.sources=www/CompuCentro_Coban \
                            -Dsonar.host.url=http://sonarqube:9000 \
                            -Dsonar.token=$SONARQUBE_TOKEN
                    """
                }
            }
        }

        stage('Esperar Resultados') {
            steps {
                script {
                    echo "⏳ Esperando Quality Gate..."
                    timeout(time: 10, unit: 'MINUTES') {
                        waitForQualityGate abortPipeline: false
                    }
                }
            }
        }

        stage('Desplegar con Docker') {
            steps {
                echo "🐳 Levantando contenedores..."
                sh """
                    docker compose down || true
                    docker compose up -d --build
                """
            }
        }
    }

    post {
        always {
            echo "🧾 Generando reporte final..."

            sh """
                mkdir -p reports

                echo "# 📋 Reporte Final de CI/CD" > reports/reporte.md
                echo "**Proyecto:** CompuCentro Cobán WebApp" >> reports/reporte.md
                echo "**Fecha:** \$(date)" >> reports/reporte.md
                echo "**Estado del Pipeline:** ${currentBuild.currentResult}" >> reports/reporte.md
                echo "" >> reports/reporte.md
                echo "## Resultados de SonarQube:" >> reports/reporte.md

                curl -s -u $SONARQUBE_TOKEN: "http://sonarqube:9000/api/measures/component?component=compucentro&metricKeys=bugs,vulnerabilities,code_smells,duplicated_lines_density,coverage" \
                | jq -r '.component.measures[] | "- " + .metric + ": " + .value' >> reports/reporte.md
            """

            archiveArtifacts artifacts: "reports/reporte.md", fingerprint: true
        }

        success {
            echo "✅ Pipeline finalizó correctamente."
        }

        failure {
            echo "⚠️ Pipeline falló pero el reporte está listo."
        }
    }
}
