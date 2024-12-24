using Models;
using SoapCore;
using Microsoft.AspNetCore.Cors;

var builder = WebApplication.CreateBuilder(args);

// Voeg de inschrijfservice toe.
builder.Services.AddSingleton<IInschrijfService, Dienst>();

builder.Services.AddCors(options =>
{
    options.AddDefaultPolicy(policy =>
    {
        policy.WithOrigins("http://localhost:8080")  // Allow frontend origin
              .AllowAnyHeader()
              .AllowAnyMethod();
    });
});

var app = builder.Build();

if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI();
}

app.UseHttpsRedirection();
app.UseCors();

// Configureer de SOAP-endpoint
app.UseSoapEndpoint<IInschrijfService>(
    "/Service.asmx", 
    new SoapEncoderOptions());

app.Run();
