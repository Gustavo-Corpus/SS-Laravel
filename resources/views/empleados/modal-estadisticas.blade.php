<div class="modal fade" id="modalEstadisticas" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Estadísticas Generales</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Métricas generales -->
                <div class="row mb-4">
                    <div class="col-md-4 text-center">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Empleados</h5>
                                <h2 id="totalEmpleados">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Calificación Promedio</h5>
                                <h2 id="promedioGlobal">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Estados</h5>
                                <h2 id="totalEstados">0</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficas -->
                <div class="row">
                    <div class="col-md-6">
                        <h5>Distribución por Estado</h5>
                        <canvas id="pieChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h5>Promedio de Calificaciones por Estado</h5>
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
