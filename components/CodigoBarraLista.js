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
                this.productos = response.data
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