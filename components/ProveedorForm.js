export default {
    template: `
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"> Añadir Proveedor </h4>
        </div>
        <div class="modal-body">

          <div class="row">
            <div class="col-md-12">
              <div class=" form-group">
                <label>RUC</label>
                <div class="row">
                  <div class="col-md-9">
                    <div class="input-group">
                        <input type="text" v-model="proveedor.Ruc" class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-warning" type="button" @click="consultarRUC">
                              Consulta RUC <span class="search"></span>
                            </button>
                        </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="Producto">Nombre del proveedor</label>
                <input type="text" class="form-control" placeholder="Proveedor" v-model="proveedor.Proveedor">
              </div>
              <div class="form-group">
                <label for="Producto">Direccion</label>
                <input type="text" class="form-control" placeholder="Dirección" v-model="proveedor.Direccion">
              </div>
              <div class="form-group">
                <label for="Producto">Observación</label>
                <input type="text" class="form-control" placeholder="Observación" v-model="proveedor.Observacion">
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
        <button class="btn btn-success" @click="guardarProveedor">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
        </div>
      </div>
      
    `,
    mounted() { 
    },
    watch: {

    },
    data() {
      return {
        proveedor: {
          Proveedor: '',
          Ruc: '',
          Direccion: '',
          Observacion: '',
          FechaReg: null
        }
      }
    },
    computed: {
    },
    methods: {
      consultarRUC() {
        axios.get('/controllers/server_consultarDNIRUC.php', {
            params: {
                type: 'RUC',
                numero: this.proveedor.Ruc
            }
        }).then((response) => {
            var data = response.data.result
            this.proveedor.Proveedor = data.RazonSocial
            this.proveedor.Direccion = data.Direccion
        })
        .catch(function (error) {
            console.log(error)
        })
      },
      guardarProveedor() {
        axios.post('/api/index.php/proveedores', this.proveedor)
        .then((response) => {
          $.notify({
              icon: 'fa fa-check',
              message: 'Proveedor guardado correctamente'
          }, {
              type: 'success'
          });
          $('#nuevo-proveedor').modal('hide')
        })
        .catch((error) => {
          $.notify({
              icon: 'fa fa-check',
              message: 'Ha ocurrido un error, vuelva a intentarlo'
          }, {
              type: 'error'
          });
        })
      }
    }
  }