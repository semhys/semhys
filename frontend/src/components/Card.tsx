import React from 'react'

export default function Card({ title, children }: { title: string; children?: React.ReactNode }) {
  return (
    <article className="rounded-md p-6 shadow-sm bg-white border-l-4 border-semhys-100 hover:shadow-lg transition-shadow">
      <h3 className="font-semibold mb-3 text-brand-dark">{title}</h3>
      <div className="text-brand-muted">{children}</div>
    </article>
  )
}
