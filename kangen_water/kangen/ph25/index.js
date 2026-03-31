import React, { useState } from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ReferenceLine, ResponsiveContainer } from 'recharts';

// Datos de simulación del pH en el tiempo (en minutos)
const data = [
    { time: 0, refresco: 7.0, ph25: 7.0, protocolo: 7.0 },
    { time: 1, refresco: 2.5, ph25: 2.5, protocolo: 2.5 }, // Impacto inicial
    { time: 2, refresco: 2.6, ph25: 3.0, protocolo: 8.5 }, // Rescate alcalino
    { time: 5, refresco: 2.8, ph25: 3.8, protocolo: 7.2 }, // Estabilización
    { time: 15, refresco: 3.5, ph25: 5.0, protocolo: 7.0 },
    { time: 25, refresco: 4.2, ph25: 5.6, protocolo: 7.0 }, // ph25 sale de la zona de peligro
    { time: 35, refresco: 4.8, ph25: 6.5, protocolo: 7.0 },
    { time: 45, refresco: 5.2, ph25: 7.0, protocolo: 7.0 },
    { time: 55, refresco: 5.8, ph25: 7.0, protocolo: 7.0 }, // Refresco sale por fin del peligro
    { time: 60, refresco: 6.2, ph25: 7.0, protocolo: 7.0 },
];

export default function SimuladorBucal() {
    const [activeCurve, setActiveCurve] = useState('protocolo');

    return (
        <div className="p-8 bg-gray-900 rounded-xl shadow-2xl max-w-4xl mx-auto text-white font-sans border border-gray-800">
            <div className="text-center mb-8">
                <h2 className="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-blue-500 mb-2">
                    Simulador de Reseteo Bucal: pH y Esmalte
                </h2>
                <p className="text-gray-400">Descubre cómo tus hábitos impactan la barrera defensiva de tus dientes.</p>
            </div>

            {/* Botones de control */}
            <div className="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                <button
                    onClick={() => setActiveCurve('refresco')}
                    className={`px-6 py-3 rounded-lg font-bold transition-all duration-300 ${activeCurve === 'refresco' ? 'bg-red-600 shadow-[0_0_15px_rgba(220,38,38,0.6)]' : 'bg-gray-800 hover:bg-gray-700 text-gray-300'}`}
                >
                    🥤 Refresco Azucarado
                </button>
                <button
                    onClick={() => setActiveCurve('ph25')}
                    className={`px-6 py-3 rounded-lg font-bold transition-all duration-300 ${activeCurve === 'ph25' ? 'bg-yellow-500 text-gray-900 shadow-[0_0_15px_rgba(234,179,8,0.6)]' : 'bg-gray-800 hover:bg-gray-700 text-gray-300'}`}
                >
                    💧 Gárgaras pH 2.5 (Sin neutralizar)
                </button>
                <button
                    onClick={() => setActiveCurve('protocolo')}
                    className={`px-6 py-3 rounded-lg font-bold transition-all duration-300 ${activeCurve === 'protocolo' ? 'bg-cyan-500 text-gray-900 shadow-[0_0_15px_rgba(6,182,212,0.6)]' : 'bg-gray-800 hover:bg-gray-700 text-gray-300'}`}
                >
                    🛡️ Protocolo Impacto Neutro (2.5 + 9.5)
                </button>
            </div>

            {/* Gráfica */}
            <div className="h-96 w-full bg-gray-950 p-4 rounded-lg border border-gray-800 shadow-inner">
                <ResponsiveContainer width="100%" height="100%">
                    <LineChart data={data} margin={{ top: 20, right: 30, left: 0, bottom: 0 }}>
                        <CartesianGrid strokeDasharray="3 3" stroke="#374151" vertical={false} />
                        <XAxis dataKey="time" stroke="#9CA3AF" label={{ value: 'Minutos', position: 'insideBottomRight', offset: -10, fill: '#9CA3AF' }} />
                        <YAxis stroke="#9CA3AF" domain={[0, 10]} ticks={[0, 2.5, 5.5, 7, 8.5, 10]} />
                        <Tooltip
                            contentStyle={{ backgroundColor: '#111827', borderColor: '#374151', borderRadius: '8px' }}
                            itemStyle={{ color: '#E5E7EB' }}
                        />
                        <Legend verticalAlign="top" height={36} />

                        {/* Línea de peligro del esmalte */}
                        <ReferenceLine y={5.5} stroke="#EF4444" strokeDasharray="5 5" strokeWidth={2} label={{ position: 'top', value: 'Límite Crítico: Desmineralización (pH 5.5)', fill: '#EF4444', fontSize: 14 }} />

                        {/* Curvas dinámicas */}
                        {activeCurve === 'refresco' && <Line type="monotone" dataKey="refresco" name="Evolución Refresco" stroke="#EF4444" strokeWidth={4} dot={{ r: 4 }} activeDot={{ r: 8 }} />}
                        {activeCurve === 'ph25' && <Line type="monotone" dataKey="ph25" name="Evolución pH 2.5 (Solo)" stroke="#EAB308" strokeWidth={4} dot={{ r: 4 }} activeDot={{ r: 8 }} />}
                        {activeCurve === 'protocolo' && <Line type="monotone" dataKey="protocolo" name="Evolución Protocolo" stroke="#06B6D4" strokeWidth={4} dot={{ r: 4 }} activeDot={{ r: 8 }} />}
                    </LineChart>
                </ResponsiveContainer>
            </div>

            {/* Explicación dinámica */}
            <div className="mt-6 p-4 bg-gray-800 rounded-lg border-l-4 border-cyan-500">
                {activeCurve === 'refresco' && <p className="text-gray-300"><strong className="text-red-400">Peligro Crítico:</strong> El azúcar alimenta a las bacterias, manteniendo el pH por debajo del umbral seguro (5.5) durante casi una hora. El esmalte se disuelve lentamente.</p>}
                {activeCurve === 'ph25' && <p className="text-gray-300"><strong className="text-yellow-400">Advertencia:</strong> El ácido hipocloroso desinfecta, pero sin neutralizar, tu boca tarda unos 25 minutos en recuperar su pH natural gracias a la saliva. Hay exposición innecesaria.</p>}
                {activeCurve === 'protocolo' && <p className="text-gray-300"><strong className="text-cyan-400">Escudo Activado:</strong> Al enjuagar inmediatamente con agua alcalina (pH 8.5/9.5), el tiempo en la zona de peligro se reduce a apenas unos segundos. Máxima desinfección, cero daños al esmalte.</p>}
            </div>
        </div>
    );
}