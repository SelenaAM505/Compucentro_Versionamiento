stage('Generar Reporte Final') {
    steps {
        script {
            echo "🧾 Generando reporte final del estado del pipeline..."

            sh """
                mkdir -p reports

                # 1) Crear archivo base en Markdown
                echo "# 📋 Reporte de Ejecución Jenkins" > reports/reporte.md
                echo "**Proyecto:** CompuCentro Cobán WebApp" >> reports/reporte.md
                echo "**Fecha:** $(date)" >> reports/reporte.md
                echo "**Estado del Pipeline:** ${currentBuild.currentResult}" >> reports/reporte.md

                echo "\n---\n## Resultados de SonarQube" >> reports/reporte.md

                # 2) Obtener métricas desde la API
                curl -s -u $SONARQUBE_TOKEN:http://sonarqube:9000 \\
                  "http://sonarqube:9000/api/measures/component?component=compucentro&metricKeys=bugs,vulnerabilities,code_smells,duplicated_lines_density,coverage" |
                jq -r '.component.measures[] | "* **\(.metric):** \(.value)"' >> reports/reporte.md

                echo "\n---\nReporte generado automáticamente." >> reports/reporte.md
            """

            // 🟢 Intentar generar PDF
            sh """
                pandoc reports/reporte.md \
                    --from markdown \
                    --template=cicd/plantillas/reporte_compucentro.latex \
                    --pdf-engine=xelatex \
                    -o reports/Reporte_SonarQube_CompuCentro.pdf \
                || echo "⚠️ No se pudo generar PDF, usando reporte en Markdown como Plan B."
            """

            // 🟡 Si el PDF NO existe → dejar el .md como reporte final
            if (!fileExists("reports/Reporte_SonarQube_CompuCentro.pdf")) {
                echo "📄 Usando reporte Markdown como archivo final (Plan B)."
                archiveArtifacts artifacts: 'reports/reporte.md', followSymlinks: false
            } else {
                echo "✅ PDF generado correctamente."
                archiveArtifacts artifacts: 'reports/Reporte_SonarQube_CompuCentro.pdf', followSymlinks: false
            }
        }
    }
}
