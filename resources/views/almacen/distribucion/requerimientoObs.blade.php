<div class="modal fade" tabindex="-1" role="dialog" id="modal-requerimiento_obs" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 500px;">
        <div class="modal-content">
            <form id="form-requerimiento_obs">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title" id="cabecera_req"></h3>
                    </div>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="obs_id_requerimiento">
                    <input type="text" class="oculto" name="boton_origen">
                    <input type="text" class="oculto" name="estado">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Ingrese su Comentario de conformidad</h5>
                            <textarea name="obs_motivo" id="obs_motivo" cols="70" rows="5" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-sm btn-success" id="btnRequerimientoObs" onClick="anular_requerimiento();" >Guardar</button> -->
                    <input type="submit" id="btnRequerimientoObs" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>