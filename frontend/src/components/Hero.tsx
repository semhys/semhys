import React from 'react'
import React from 'react'
import Button from '../components/Button'

export default function Hero({ title, subtitle }: { title: string; subtitle?: string }) {
  return (
    <section className="py-16 bg-gradient-to-b from-white to-gray-50">
      <div className="container mx-auto px-4 grid gap-8 grid-cols-1 md:grid-cols-2 items-center">
        <div>
          <h1 className="text-4xl md:text-5xl font-extrabold mb-4 text-brand-dark">{title}</h1>
          {subtitle && <p className="text-lg text-brand-muted mb-6">{subtitle}</p>}
          <div className="flex flex-col sm:flex-row gap-3">
                <a href="/contact" className="btn btn-primary text-center">Contáctanos</a>
                <Button variant="accent">Nuestros servicios</Button>
          </div>
        </div>
        <div className="flex justify-center md:justify-end">
          <div className="w-44 h-44 md:w-64 md:h-64 rounded-xl shadow-brand-lg flex items-center justify-center animate-float" style={{background:'linear-gradient(135deg,var(--semhys-accent),var(--semhys-primary))'}}>
            <img src="/assets/img/logo.svg" alt="logo" className="w-20 h-20 md:w-28 md:h-28 opacity-90"/>
          </div>
        </div>
      </div>
    </section>
  )
}
