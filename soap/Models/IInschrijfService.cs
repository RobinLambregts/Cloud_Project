using System.ServiceModel;
namespace Models;

[ServiceContract]
public interface IInschrijfService
{
    [OperationContract]
    string SchrijfIn(string naam, string sport);

    [OperationContract]
    Dictionary<string, List<string>> GetInschrijvingen();
}
