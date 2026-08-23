import { Head, Link } from '@inertiajs/react';
import FrontendLayout from '@/Layouts/FrontendLayout';

export default function Page({ page, title, slug }) {
    const isPrivacyPolicy = slug === 'privacy-policy' || title.toLowerCase().includes('privacy');
    const kicker = isPrivacyPolicy ? 'Privacy and data' : 'NovaMart information';
    const intro = isPrivacyPolicy
        ? 'Understand how NovaMart handles the information that helps us provide a dependable marketplace experience.'
        : `Everything you need to know about ${title}, presented in one clear place.`;

    return (
        <FrontendLayout>
            <Head title={`${title} | NovaMart`} />

            <main className="market-page pb-16 sm:pb-20">
                <section className="market-container pt-8 sm:pt-10">
                    <div className="market-hero relative isolate overflow-hidden px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
                        <div className="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-fuchsia-400/25 blur-3xl" />
                        <div className="absolute -bottom-24 left-1/3 h-56 w-56 rounded-full bg-violet-300/25 blur-3xl" />
                        <div className="relative max-w-3xl">
                            <p className="text-[11px] font-extrabold uppercase tracking-[0.2em] text-violet-200">{kicker}</p>
                            <h1 className="mt-4 text-4xl font-extrabold tracking-[-0.06em] text-white sm:text-5xl">{title}</h1>
                            <p className="mt-5 max-w-2xl text-base leading-7 text-slate-200 sm:text-lg">{intro}</p>
                            <div className="mt-7 flex flex-wrap gap-3">
                                <Link href={route('products.index')} className="market-button-light">Browse products</Link>
                                <Link href={route('rfq.create')} className="market-button-dark-secondary">Ask our team</Link>
                            </div>
                        </div>
                    </div>
                </section>

                <article className="market-container mt-6 sm:mt-8">
                    <div className="market-panel p-6 sm:p-10 lg:p-14">
                        <div
                            className="prose prose-slate max-w-none prose-headings:font-extrabold prose-headings:tracking-[-0.035em] prose-a:font-bold prose-a:text-violet-700 hover:prose-a:text-violet-900 prose-p:leading-8 prose-li:leading-7"
                            dangerouslySetInnerHTML={{ __html: page.content }}
                        />
                    </div>
                </article>
            </main>
        </FrontendLayout>
    );
}
