import React from 'react'
import Hero from '../components/Hero'
import Card from '../components/Card'
import Button from '../components/Button'

export default function Home() {
  return (
    <div>
      <Hero title="SEMHYS — Ingeniería y Soluciones" subtitle="Soluciones hidráulicas, eléctricas y de control con respaldo técnico." />
      <section className="container mx-auto px-4 py-12">
        <div className="grid gap-6 grid-cols-1 md:grid-cols-3">
          <Card title="Servicios">Consultoría, diseño y mantenimiento para sistemas hidráulicos y eléctricos.</Card>
          <Card title="Proyectos">Casos de estudio y portafolio con resultados medibles.</Card>
          <Card title="Contacto">Equipo técnico y comercial listo para ayudarte.</Card>
        </div>
        <div className="mt-10 flex justify-center">
          <Button variant="primary">Solicitar cotización</Button>
        </div>
      </section>
    </div>
  )
}
