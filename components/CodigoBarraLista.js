import CodigoBarra from './CodigoBarra.js';


export default {
    template: `
      <div id="barcode">
        <span v-for="(producto, key) in productos">
            <codigo-barra v-for="n in producto.Cantidad" :producto="producto" :key="n + producto.IdProducto"></codigo-barra>
        </span>
      </div>
    `,
    mounted() {
        this.obtenerProductos()
    },
    data() {
      return {
          productos: []
      }
    },
    props: ['hashMovimiento'],
    methods: {
        obtenerProductos () {
            axios.get('/api/index.php/movimiento/productos', {
                params: {
                    hash: this.hashMovimiento
                }
            }).then((response) => {
                var productos = response.data.map(function(x) {
                    x.Producto = x.Producto.split('-').slice(0, -1).join()
                    return x
                })
                
                this.productos = productos
              })
              .catch(function (error) {
                console.log(error)
              })
        }
    },
    components: {
        CodigoBarra
    }
  }