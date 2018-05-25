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
                    <button type="button" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
                  </div>
                </div>
                
              </div>
              <div class=" form-group">
                <label>Marca</label>

                <div class="row">
                  <div class="col-md-9">
                    <v-select v-model="producto.marca" :options="marcas">
                      <span slot="no-options">No se encontró, por favor agreguelo.</span>
                    </v-select>
                  </div>
                  <div class="col-md-3">
                    <button type="button" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
                  </div>
                </div>
                
              </div>
              <div class="form-group">
                <label for="Producto">Producto</label>
                <input type="text" class="form-control" id="Producto" name="producto" placeholder="Producto">
              </div>
              <div class="form-group">
                <label for="Producto">Genero</label>
                <input type="text" class="form-control" id="Producto" name="producto" placeholder="Producto">
              </div>
              <div class="form-group">
              <label for="Producto">Botapie</label>
              <input type="text" class="form-control" id="Producto" name="producto" placeholder="Producto">
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
                    <button type="button" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
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
                    <button type="button" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
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
                    <button type="button" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
        <button class="btn btn-success" id="btnBuscarFactura">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
        </div>
      </div>
      
    `,
    mounted() {
      this.obtenerCategorias()
      this.obtenerMarcas()
      this.obtenerMediciones()
      this.obtenerModelos()
    },
    data() {
      return {
        tallas: [],
        modelos: [],
        mediciones: [],
        categorias: [],
        marcas: [],
        message: 'asdasd',
        producto: {
          IdProductoMarca: null,
          IdProductoMedicion: null,
          IdProductoCategoria: null,
          IdProductoModelo: null,
          IdProductoBotapie: null,
          IdProductoGenero: null,
          IdProductoTalla: '',
          Producto: '',
          CodigoBarra: '',
          Color: '',
          PorcentajeUtilidad: '',
          categoria: null,
          marca: null,
          modelo: null,
          talla: null
        }
      }
    },
    methods: {
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
        alert('se guardo')
      }
    }
  }