export default {
    template: `
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"> Añadir Producto </h4>
        </div>
        <div class="modal-body">

          <div class="row">
            <div class="col-md-6">
              <div class=" form-group">
                <label>Categoría</label>

                <div class="row">
                  <div class="col-md-9">
                    <v-select v-model="producto.categoria" :options="categorias">
                      <span slot="no-options">No se encontró, por favor agreguelo.</span>
                    </v-select>
                  </div>
                  <div class="col-md-3">
                    <button type="button" class="btn btn-warning" @click="obtenerCategorias"><i class="fa fa-refresh"></i></button>
                  </div>
                </div>
                
              </div>
              <div class=" form-group">
                <label>Marca</label>

                <div class="row">
                  <div class="col-md-9">
                    <v-select v-model="producto.marca" :options="marcas">
                      <span slot="no-options">No se encontró, por favor agréguelo.</span>
                    </v-select>
                  </div>
                  <div class="col-md-3">
                    <button type="button" class="btn btn-warning" @click="obtenerMarcas"><i class="fa fa-refresh"></i></button>                    
                  </div>
                </div>
                
              </div>
              <div class="form-group">
                <label for="Producto">Producto</label>
                <input type="text" class="form-control" placeholder="Producto" v-model="producto.Producto">
              </div>
              <div class="form-group">
                <label for="Producto">Genero</label>
                <v-select v-model="producto.Genero" :options="generos">
                  <span slot="no-options">No se encontró, por favor agréguelo.</span>
                </v-select>
              </div>
              <div class="form-group">
              <label for="Producto">Botapie</label>
              <input type="text" class="form-control" placeholder="Botapie" v-model="producto.Botapie">
            </div>
            </div>
            <div class="col-md-6">
              <div class=" form-group">
                <label>Medición</label>
                
                <div class="row">
                  <div class="col-md-9">
                    <v-select v-model="producto.medicion" :options="mediciones">
                      <span slot="no-options">No se encontró, por favor agreguelo.</span>
                    </v-select>
                  </div>
                  <div class="col-md-3">
                    <button type="button" class="btn btn-warning" @click="obtenerMediciones"><i class="fa fa-refresh"></i></button>
                  </div>
                </div>

              </div>
              <div class=" form-group">
                <label>Modelo</label>
                
                <div class="row">
                  <div class="col-md-9">
                    <v-select v-model="producto.modelo" :options="modelos">
                      <span slot="no-options">No se encontró, por favor agreguelo.</span>
                    </v-select>
                  </div>
                  <div class="col-md-3">
                    <button type="button" class="btn btn-warning" @click="obtenerModelos"><i class="fa fa-refresh"></i></button>
                  </div>
                </div>

              </div>
              <div class=" form-group">
                <label>Colores</label>
                <input type="text" class="form-control"  placeholder="Colores" v-model="producto.Color">
              </div>
              <div class=" form-group">
                <label>Talla</label>
                
                <div class="row">
                  <div class="col-md-9">
                    <v-select v-model="producto.talla" :options="tallas">
                      <span slot="no-options">No se encontró, por favor agreguelo.</span>
                    </v-select>
                  </div>
                  <div class="col-md-3">
                    <button type="button" class="btn btn-warning" @click="obtenerTallas"><i class="fa fa-refresh"></i></button>
                  </div>
                </div>

              </div>
              <div class=" form-group">
                <label>Porcentaje Utilidad</label>
                <input type="number" class="form-control"  placeholder="Porcentaje Utilidad" v-model="producto.PorcentajeUtilidad">
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
        <button class="btn btn-success" @click="guardarProducto">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
        </div>
      </div>
      
    `,
    mounted () {
      this.obtenerCategorias()
      this.obtenerMarcas()
      this.obtenerMediciones()
      this.obtenerModelos()
      this.obtenerTallas()
    },
    watch: {

    },
    data() {
      return {
        tallas: [],
        modelos: [],
        mediciones: [],
        categorias: [],
        marcas: [],
        generos: ['VARON', 'DAMA'],
        message: 'asdasd',
        producto: {
          IdProductoMarca: null,
          IdProductoMedicion: null,
          IdProductoCategoria: null,
          IdProductoModelo: null,
          // IdProductoBotapie: null,
          IdProductoTalla: '',
          Producto: '',
          CodigoBarra: '',
          Color: '',
          PorcentajeUtilidad: '',
          Botapie: '',
          Genero: '',
          categoria: null,
          marca: null,
          modelo: null,
          talla: null
        }
      }
    },
    computed: {
    },
    methods: {
      generarCodigoBarra() {

      },
      clearForm() {
        this.producto.Producto = ''
        this.producto.Color = ''
        this.producto.Genero = ''
        this.producto.talla = ''
      },
      obtenerTallas() {
        axios.get('/api/index.php/tallas').then((response) => {
          this.tallas = response.data.map(function(talla) {
            talla.label = talla.ProductoTalla
            return talla
          })
        })
        .catch(function (error) {
          console.log(error)
        })
      },
      obtenerModelos() {
        axios.get('/api/index.php/modelos').then((response) => {
          this.modelos = response.data.map(function(modelo) {
            modelo.label = modelo.ProductoModelo
            return modelo
          })
        })
        .catch(function (error) {
          console.log(error)
        })
      },
      obtenerMediciones() {
        axios.get('/api/index.php/mediciones').then((response) => {
          this.producto.medicion = response.data[0]
          this.mediciones = response.data.map(function(medicion) {
            medicion.label = medicion.ProductoMedicion
            return medicion
          })
        })
        .catch(function (error) {
          console.log(error)
        })
      },
      obtenerCategorias() {
        axios.get('/api/index.php/categorias').then((response) => {
          this.categorias = response.data.map(function(cat) {
            cat.label = cat.ProductoCategoria
            return cat
          })
        })
        .catch(function (error) {
          console.log(error)
        })
      },
      obtenerMarcas() {
        axios.get('/api/index.php/marcas').then((response) => {
          this.marcas = response.data.map(function(marca) {
            marca.label = marca.ProductoMarca
            return marca
          })
        })
        .catch(function (error) {
          console.log(error)
        })
      },
      guardarProducto() {
        axios.post('/api/index.php/productos', this.producto)
        .then((response) => {
          $.notify({
              icon: 'fa fa-check',
              message: 'Producto guardado correctamente'
          }, {
              type: 'success'
          });
          $('#nuevo-producto').modal('hide')
          this.clearForm()
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