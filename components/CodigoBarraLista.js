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
        if (this.idProducto) {
            this.obtenerProducto()
        } else {
            this.obtenerProductos()
        }
    },
    data() {
      return {
          productos: []
      }
    },
    props: ['hashMovimiento', 'idProducto', 'cantidad'],
    methods: {
        obtenerProducto () {
            axios.get('/api/index.php/productos/id/' + this.idProducto)
                .then((response) => {
                    var producto = response.data
                    producto.Producto = producto.Producto
                    producto.Cantidad = parseInt(this.cantidad)

                    this.productos.push(producto)
                })
                .catch(function (error) {
                    console.log(error)
                })
        },
        obtenerProductos () {
            axios.get('/api/index.php/movimiento/productos', {
                params: {
                    hash: this.hashMovimiento
                }
            }).then((response) => {
                var productos = response.data.map(function(x) {
                    x.Producto = x.Producto
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