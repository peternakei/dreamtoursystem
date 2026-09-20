import net from 'node:net'

const host = '127.0.0.1'
const services = [{name: 'Vue frontend', port: 5174}, {name: 'Laravel backend', port: 8001}]
const wait = process.argv.includes('--wait')

function available(port) {
  return new Promise((resolve, reject) => {
    const server = net.createServer()
    server.once('error', error => error.code === 'EADDRINUSE' ? resolve(false) : reject(error))
    server.listen({host, port, exclusive: true}, () => server.close(() => resolve(true)))
  })
}
function listening(port) {
  return new Promise(resolve => {
    const socket = net.connect({host, port})
    const finish = result => { socket.destroy(); resolve(result) }
    socket.setTimeout(300)
    socket.once('connect', () => finish(true))
    socket.once('error', () => finish(false))
    socket.once('timeout', () => finish(false))
  })
}

try {
  if (wait) {
    const deadline = Date.now() + 15000
    while (Date.now() < deadline) {
      if ((await Promise.all(services.map(s => listening(s.port)))).every(Boolean)) process.exit(0)
      await new Promise(resolve => setTimeout(resolve, 150))
    }
    console.error('The development servers did not become ready. See the server errors above.')
    process.exitCode = 1
  } else {
    const free = await Promise.all(services.map(s => available(s.port)))
    const busy = services.filter((_, index) => !free[index])
    if (busy.length) {
      for (const service of busy) console.error(`${service.name}: port ${service.port} is already in use.`)
      console.error('If this project is already running, open http://127.0.0.1:5174.')
      console.error('Otherwise stop its previous terminal with Ctrl+C before running ./start.sh again.')
      console.error("Find the listening processes with: ss -ltnp '( sport = :5174 or sport = :8001 )'")
      process.exitCode = 1
    }
  }
} catch (error) {
  console.error(`Unable to check development ports: ${error.message}`)
  process.exitCode = 1
}
